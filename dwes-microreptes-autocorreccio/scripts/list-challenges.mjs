import { readdir, readFile } from 'node:fs/promises';
import path from 'node:path';
import process from 'node:process';

const microreptesDir = path.join(process.cwd(), 'microreptes');

async function main() {
  const entries = await readdir(microreptesDir, { withFileTypes: true });
  const dirs = entries.filter((entry) => entry.isDirectory()).map((entry) => entry.name).sort();
  console.log('Microreptes disponibles:');
  for (const dir of dirs) {
    const challenge = JSON.parse(await readFile(path.join(microreptesDir, dir, 'challenge.json'), 'utf8'));
    console.log(`- ${challenge.challenge_id}: ${challenge.title}`);
  }
}

main().catch((error) => {
  console.error(`No s'han pogut llistar els microreptes: ${error.message}`);
  process.exit(1);
});
