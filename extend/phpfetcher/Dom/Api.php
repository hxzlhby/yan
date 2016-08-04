<?php
/*
 * @author xuruiqi
 * @date   2014.09.21
 * @copyright reetsee.com
 * @desc   Dom's abstract class
 */
namespace phpfetcher\Dom;
abstract class Api {
    abstract function getElementById($id);

    abstract function getElementsByTagName($tag);

    abstract function loadHTML($content);

    abstract function sel($pattern = '', $node = NULL);
}
