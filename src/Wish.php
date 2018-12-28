<?php
namespace XiangYu2038\Wish;
  class Wish
  {

      protected $with;
      protected $delete = [];
      protected $current = '';
      protected  $wish = [] ;
      protected $current_realation = 'self';///默认为self

      public function with($with){
          $this -> with = $with;
          return $this;
      }



      public function wish($wish){
          $this ->wish[$wish]['add'] = [];
          $this ->wish[$wish]['only'] = [];
          $this ->wish[$wish]['except'] = [];
          $this ->wish[$wish]['delete'] = [];
          $this ->current = $wish;
          return $this;
      }

      public function add($add){
          if(!$this -> wish){
              $this ->wish('self');
          }

          $this->wish[$this ->current]['add'] = array_merge($this->wish[$this ->current]['add'],func_get_args());
          return $this;
      }
      public function except(){
          if(!$this -> wish){
              $this ->wish('self');
          }
          $this->wish[$this ->current]['except'] = array_merge($this->wish[$this ->current]['except'],func_get_args());
          return $this;
      }

      public function only(){
          if(!$this -> wish){
              $this ->wish('self');
          }
          $this->wish[$this ->current]['only'] = array_merge($this->wish[$this ->current]['only'],func_get_args());
          return $this;
      }

      public function get(){

          if($this->with instanceof \Illuminate\Database\Eloquent\Model){
              $this -> self($this->with);///首先对本身进行处理

              $this -> relation($this -> with);
             // $this -> setDelete();
              return $this -> with;
          }

          foreach ($this->with as $v){
              $this -> self($v);///首先对本身进行处理
              $this -> relation($v);
          }
          //$this -> setDelete();
          return $this->with;
      }

      public function setAdd($model,$add){

          foreach ($add as $v){
              $f = 'set'.ucfirst(convertUnderline($v));

              $model->setAttribute($v,$model->$f());
          }

      }
      public function setOnly($model,$only){
          if($only){
              $model->setRawAttributes([]);///清空
          }
          foreach ($only as $v){
              $model->setAttribute($v,$model->getOriginal($v));
          }

      }

      public function setExcept($model,$except){

          foreach ($except as $v){
              unset($model->$v);
          }
      }

      public function self($model){

          if(isset($this -> wish['self'])){
              $this -> setAll($model,'self');
          }

      }

      public function relation($model){

          foreach ( $model -> getrelations() as $key=> $v){

              if(array_key_exists($key,$this -> wish)) {
                  $this -> current_realation = $key;//当前操作的关系
                  if ($v instanceof \Illuminate\Database\Eloquent\Model) {
                      $this->setAll($v, $key);

                      $this->relation($v);
                      continue;
                  }

                  foreach ($v as $vv) {

                      $this->setAll($vv, $key);

                      $this->relation($vv);


                  }
              }
          }
      }

      public function setAll($model,$wish){

          $this -> setAdd($model,$this -> wish[$wish]['add']);
          $this -> setOnly($model,$this -> wish[$wish]['only']);
          $this -> setExcept($model,$this -> wish[$wish]['except']);
          $this -> setDelete($model,$this -> wish[$wish]['delete']);

      }
      public function setAdds($model,$add){

          foreach ($add->getAttributes() as $key => $v){
              $model->setAttribute($key,$v);
          }

      }


      public function delete($realation,$flag=false){
          if(!$this -> wish){
             $this ->wish('self');
          }

          $arg = [$realation,$flag];
           array_push($this->wish[$this ->current]['delete'],$arg);

          return $this;

      }
      public function setDelete($model,$delete){

            $current = $this -> current_realation;

          foreach ($this -> wish[$current]['delete'] as $v){
              $re = $v[0];
              if($v[1]){
                   ///如果全部删除
                   $this -> setExcept($model,$re);
               }else{
                   //////部分删除


                   $value = $model -> $re;
                   $this -> setAdds($model,$value);
                   unset($model->$re);
               }
          }
      }

  }