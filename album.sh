#!/bin/bash
DIR="$(dirname "$(readlink -f "$0")")"
php $DIR/album.php $1
