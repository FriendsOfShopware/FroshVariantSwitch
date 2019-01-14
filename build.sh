#!/usr/bin/env bash

commit=$1
if [ -z ${commit} ]; then
    commit=$(git tag | tail -n 1)
    if [ -z ${commit} ]; then
        commit="master";
    fi
fi

# Remove old release
rm -rf FroshVariantSwitch FroshVariantSwitch-*.zip

# Build new release
mkdir -p FroshVariantSwitch
git archive ${commit} | tar -x -C FroshVariantSwitch
composer install --no-dev -n -o -d FroshVariantSwitch
zip -r FroshVariantSwitch-${commit}.zip FroshVariantSwitch