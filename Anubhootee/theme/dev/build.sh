#!/bin/sh
# Build the installable theme zip: theme/build/anubhootee.zip
set -e
cd "$(dirname "$0")/.."
mkdir -p build
rm -f build/anubhootee.zip
zip -rq build/anubhootee.zip anubhootee -x '*.DS_Store'
echo "Built $(pwd)/build/anubhootee.zip ($(du -h build/anubhootee.zip | cut -f1))"
