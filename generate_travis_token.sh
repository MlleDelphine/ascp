#!/bin/bash
if [ "$TRAVIS_SECURE_ENV_VARS" = "true" ];
then
    echo '{ "config": {"github-oauth":{"github.com": ' > ~/.composer/config.json
    echo "\"$GH_OAUTH\"" >> ~/.composer/config.json
    echo '}}}' >> ~/.composer/config.json
fi

