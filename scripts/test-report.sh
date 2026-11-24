#!/bin/sh
set -eu
root=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
cd "$root"
mkdir -p reports/tests
status=0
vendor/bin/phpunit --log-junit reports/tests/junit.xml --testdox-text reports/tests/testdox.txt --no-progress > reports/tests/summary.txt 2>&1 || status=$?
sed -i "s#$root/##g; s#$root#.#g" reports/tests/junit.xml reports/tests/testdox.txt reports/tests/summary.txt
exit "$status"
