<?php
namespace XY;
use Illuminate\Support\Facades\Facade;


class XY extends Facade
{
    /**
     * 继承自父类抽象方法
     * @param
     * @return mixed
     */
    protected static function getFacadeAccessor() {
        return Wish::class;
    }


}