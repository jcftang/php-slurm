#!/bin/bash
VERSION=$(git describe)

git archive --format tar --prefix $VERSION/ HEAD | bzip2 -c > $VERSION.tar.bz2
