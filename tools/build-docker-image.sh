#!/bin/sh

set -e

if [[ -f /.dockerenv ]]; then
    echo "You can not run this shell script inside a docker container"
    exit 1
fi

cd "$(dirname "$0")/.."

docker build -t plas .
