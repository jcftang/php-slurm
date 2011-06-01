#!/bin/bash
#VERSION=$(git describe)
VERSION=$(git tag -l | tail -1)
DISTNAME=php-slurm
DISTFILE=$DISTNAME-$VERSION

git archive --format tar --prefix $DISTFILE/ $VERSION | bzip2 -c > $DISTFILE.tar.bz2
