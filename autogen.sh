#!/bin/bash

set -e

if [ -z "$AUTOGEN_REPO" ]; then
    echo "[ERROR] AUTOGEN_REPO environment variable is not set"
    exit 1
fi

if [ ! -d "batch_process_sdk_autogen" ]; then
    git clone "$AUTOGEN_REPO" batch_process_sdk_autogen
else
    (
        cd batch_process_sdk_autogen
        git pull
    )
fi

bash batch_process_sdk_autogen/scripts/generate.sh php_telesign