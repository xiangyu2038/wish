## wish laravel的查询结果处理插件

这个插件是针对laravel的ORM模型查询的结果进行处理的插件,用户可以使用此插件轻松对laravel的ORM结果进行处理,以便方便的获取自己想要的数据格式 

### 安装
 ```sh
 composer require xiangyu2038/wish
 ```
### 示例代码1

```php
当前处理结果数据添加一个字段  test
<?php
 namespace App\Http;
 use XiangYu2038\Wish\XY;

  class TestController {
      public function index(Request $request){
          $test_model =   TestModel::first();///查询出一条数据
          $test_model =  XY::with($test_model) ->add('test') ->get();///$test_model 添加一个字段test

      }
  }
  ```
当前模型添加test字段后 需要在当前模型内定义getTest方法
示例

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    //
    //指定表名
    protected $table = 'a_table';

    protected $guarded = [];
    public function getTest(){
    return 'test';
}



///属性转换
    //    protected $casts = [

}

```
 此时 testModel 模型会增加一个字段test  且其值为test

支持对字段链式操作 代码示例 
```php
$res = XY::with($model)->except('code','box_id','stock_id')->delete('stockDetail',true)->wish('boxDetail')->add('stock_sn')->add('box_sn')->delete('box',true)->except('box_id')->wish('box')->add('stock_sn')->wish('stockBox')->add('stock_sn')->get()->toArray();
```
此段代码的意思为对当前传入的模型删除三个多余的字段,并期望其关联关系添加某些字段 或删除某些字段或删除整个关联关系 或把关联关系字段移到上一层





 [我的github地址 ](https://github.com/xiangyu2038/).
