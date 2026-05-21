import { readdir, readFile } from 'node:fs/promises';
import path from 'node:path';
import process from 'node:process';

const rootDir = process.cwd();
const microreptesDir = path.join(rootDir, 'microreptes');
const globalDir = path.join(rootDir, 'global');
const courseDir = path.join(rootDir, 'course');
const requiredDimensions = ['functional_resolution', 'verification', 'traceability', 'documentation', 'ai_usage', 'code_quality'];

async function readJson(filePath) {
  return JSON.parse(await readFile(filePath, 'utf8'));
}

async function exists(filePath) {
  try {
    await readFile(filePath, 'utf8');
    return true;
  } catch {
    return false;
  }
}

async function validateGlobal(errors) {
  for (const fileName of ['policies.json', 'feedback-style.md', 'grading-schema.json']) {
    if (!(await exists(path.join(globalDir, fileName)))) errors.push(`global: falta ${fileName}`);
  }
  if (!(await exists(path.join(courseDir, 'active-challenges.json')))) {
    errors.push('course: falta active-challenges.json');
  }
}

function validateDimensions(rubric, challengeName, errors) {
  const dimensions = Array.isArray(rubric.dimensions) ? rubric.dimensions : [];
  const ids = new Set(dimensions.map((dimension) => dimension.id));
  for (const required of requiredDimensions) {
    if (!ids.has(required)) errors.push(`${challengeName}: falta la dimensio ${required}`);
  }
}

async function validateChallenge(challengeName, errors) {
  const dir = path.join(microreptesDir, challengeName);
  const challengePath = path.join(dir, 'challenge.json');
  const rubricPath = path.join(dir, 'rubric.json');
  if (!(await exists(challengePath))) return errors.push(`${challengeName}: falta challenge.json`);
  if (!(await exists(rubricPath))) return errors.push(`${challengeName}: falta rubric.json`);

  const challenge = await readJson(challengePath);
  const rubric = await readJson(rubricPath);
  if (challenge.challenge_id !== rubric.challenge_id) errors.push(`${challengeName}: challenge_id no coincideix`);
  validateDimensions(rubric, challengeName, errors);
  return { id: challenge.challenge_id, title: challenge.title };
}

async function main() {
  const errors = [];
  const valid = [];
  await validateGlobal(errors);
  const entries = await readdir(microreptesDir, { withFileTypes: true });
  for (const entry of entries.filter((item) => item.isDirectory()).sort((a, b) => a.name.localeCompare(b.name))) {
    const result = await validateChallenge(entry.name, errors);
    if (result) valid.push(result);
  }
  if (errors.length) {
    console.error('Validacio fallida:');
    for (const error of errors) console.error(`- ${error}`);
    process.exit(1);
  }
  console.log('Configuracio valida.');
  console.log(`Microreptes validats: ${valid.length}`);
  for (const challenge of valid) console.log(`- ${challenge.id}: ${challenge.title}`);
}

main().catch((error) => {
  console.error(`Error inesperat: ${error.message}`);
  process.exit(1);
});
