<?php namespace App\Transformer;


class UserTransformer
{

    public function TransformCollection($items)
    {
        return array_map([$this,'transform'],$items);
    }

    public function transform ($users)
    {
        return[
            'id' =>$users['id'],
            'name' => $users['name'],
            'email'  => $users['email']
        ];
    }
}
