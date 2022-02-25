#!/bin/sh

dir=$(cd `dirname $0` && pwd)

if [ $# -ge 1 ]; then
  runTests=$1
  shift
else
  runTests=$dir
fi

$dir/../vendor/bin/tester -p php -C $runTests $@
