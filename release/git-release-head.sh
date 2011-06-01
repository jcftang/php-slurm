#!/bin/bash
#VERSION=$(git describe)
VERSION=$(git tag -l | tail -1)
DISTNAME=php-slurm
DISTFILE=$DISTNAME-$VERSION

git archive --format tar --prefix $DISTFILE/ HEAD | bzip2 -c > $DISTFILE.tar.bz2
rpmbuild -ts $DISTFILE.tar.bz2
