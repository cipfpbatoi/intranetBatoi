<?php

namespace Intranet\Services;

use Illuminate\Support\{Arr, Facades\File, Str};
use LSNepomuceno\LaravelA1PdfSign\Entities\ValidatedSignedPDF;
use LSNepomuceno\LaravelA1PdfSign\Exceptions\{FileNotFoundException,
    HasNoSignatureOrInvalidPkcs7Exception,
    InvalidPdfFileException,
    ProcessRunTimeException
};
use Throwable;
class ValidatePdfMultipleSignatures
{
    private string $pdfPath, $plainTextContent, $pkcs7Path = '';

    /**
     * @throws Throwable
     */
    public static function from(string $pdfPath): Array
    {
        return (new static)->setPdfPath($pdfPath)
            ->extractSignatureData()
            ->convertSignatureDataToPlainText()
            ->convertPlainTextToObject();
    }

    /**
     * @throws FileNotFoundException
     * @throws InvalidPdfFileException
     */
    private function setPdfPath(string $pdfPath): self
    {
        if (!Str::of($pdfPath)->lower()->endsWith('.pdf')) {
            throw new InvalidPdfFileException($pdfPath);
        }

        if (!File::exists($pdfPath)) {
            throw new FileNotFoundException($pdfPath);
        }

        $this->pdfPath = $pdfPath;

        return $this;
    }

    /**
     * @throws HasNoSignatureOrInvalidPkcs7Exception
     */
    private function extractSignatureData(): self
    {
        $content = File::get($this->pdfPath);
        $regexp  = '#ByteRange\[\s*(\d+) (\d+) (\d+) (\d+)\s*\]#'; // Adjusted to match the full ByteRange pattern
        $result  = [];
        preg_match_all($regexp, $content, $result);
        dd($result);


        // Check if there are any matches
        if (empty($result[0])) {
            throw new HasNoSignatureOrInvalidPkcs7Exception($this->pdfPath);
        }

        $this->pkcs7Paths = []; // Assume this is an array now

        // Iterate over all matches
        foreach ($result[1] as $index => $start) {
            $end = $result[3][$index]; // Adjusted to the third group in the pattern

            if ($stream = fopen($this->pdfPath, 'rb')) {
                fseek($stream, $start);
                $signature = fread($stream, $end - $start); // directly read the signature bytes
                fclose($stream);

                // Assuming a1TempDir creates a temporary directory and returns a path
                $pkcs7Path = a1TempDir(tempFile: true, fileExt: '.pkcs7');
                File::put($pkcs7Path, $signature);
                $this->pkcs7Paths[] = $pkcs7Path; // Store the path for later processing
            }
        }

        return $this;

    }

    /**
     * @throws FileNotFoundException
     * @throws HasNoSignatureOrInvalidPkcs7Exception
     * @throws ProcessRunTimeException
     */
    private function convertSignatureDataToPlainText(): self
    {
        $this->plainTextContents = [];

        foreach ($this->pkcs7Paths as $pkcs7Path) {
            $output         = a1TempDir(tempFile: true, fileExt: '.txt');
            $openSslCommand = "openssl pkcs7 -in {$pkcs7Path} -inform DER -print_certs > {$output}";

            runCliCommandProcesses($openSslCommand);

            if (!File::exists($output)) {
                throw new FileNotFoundException($output);
            }

            $this->plainTextContents[] = File::get($output);

            File::delete([$output, $pkcs7Path]);
        }

        return $this;
    }

    private function convertPlainTextToObject(): array
    {
        $validatedSignatures = [];

        foreach ($this->plainTextContents as $content) {
            $finalContent = [];
            $delimiter    = '|CROP|';
            $content      = preg_replace('/(-----BEGIN .+?-----(?s).+?-----END .+?-----)/mi', $delimiter, $content);
            $content      = preg_replace('/(\s\s+|\n|\r)/', ' ', $content);
            $content      = array_filter(explode($delimiter, $content), 'trim');

            foreach ($content as $data) {
                $certInfo = $this->processDataToInfo($data);
                if (!empty($certInfo)) {
                    $finalContent[] = $certInfo;
                }
            }

            $isValid = !!count(array_intersect_key(array_flip(['OU', 'CN']), $finalContent));
            $validatedSignatures[] = new ValidatedSignedPDF($isValid, Arr::except($finalContent, 'validated'));
        }

        return $validatedSignatures;
    }

    private function processDataToInfo(string $data): array
    {
        $data = explode(', ', trim($data));

        $finalData = [];

        foreach ($data as $info) {
            $infoTemp = explode(' = ', trim($info));
            if (isset($infoTemp[0]) && $infoTemp[1]) {
                $finalData[] = [$infoTemp[0] => $infoTemp[1]];
            }
        }
        return $finalData;
    }
}
