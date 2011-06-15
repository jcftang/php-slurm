#!/bin/bash

VERSION=$(cat VERSION)

git archive --format tar --prefix $VERSION/ HEAD | bzip2 -c > $VERSION.tar.bz2
