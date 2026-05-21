import { readFile } from 'node:fs/promises';
import path from 'node:path';
import process from 'node:process';

function parseArgs(argv) {
  const args = {};
  for (let index = 0; index < argv.length; index += 1) {
    const key = argv[index];
    const value = argv[index + 1];
    if ((key === '--student' || key === '--group') && value && !value.startsWith('--')) {
      args[key.slice(2)] = value;
      index += 1;
    }
  }
  return args;
}

async function main() {
  const { student, group } = parseArgs(process.argv.slice(2));
  if (!student || !group) {
    console.error('Ús: node scripts/resolve-active-challenge.mjs --student <repo-o-id> --group <grup>');
    process.exit(1);
  }

  const configPath = path.join(process.cwd(), 'course', 'active-challenges.json');
  const config = JSON.parse(await readFile(configPath, 'utf8'));
  const studentAssignment = config.students?.[student];
  if (studentAssignment?.challenge_id) {
    console.log(studentAssignment.challenge_id);
    return;
  }

  const groupAssignment = config.groups?.[group];
  if (groupAssignment?.challenge_id) {
    console.log(groupAssignment.challenge_id);
    return;
  }

  console.error(`No s'ha trobat microrepte actiu per a student=${student} group=${group}`);
  process.exit(1);
}

main().catch((error) => {
  console.error(`No s'ha pogut resoldre el microrepte actiu: ${error.message}`);
  process.exit(1);
});
