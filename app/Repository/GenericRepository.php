<?php

namespace App\Repository;

class GenericRepository
{

    public function getPaginatedData($query_or_eloquent_builder_callback, $id_to_encode = null)
    {
        $current_page = request()->get('current_page', 1);

        $per_page = 10;

        $starting_point = ($current_page * $per_page) - $per_page;

        $data = $query_or_eloquent_builder_callback()
            //->toSql();
            //->getBindings();
            ->offset($starting_point)->limit($per_page)->get()->toArray();

        if ($id_to_encode) {

            $hash_generator = getHashidsObj();

            // encoding Ids before sending response
            foreach ($data as $key => $obj) {
                // Laravel returns an array of objects instead of an array of arrays in case of Query Builder this is why the $obj is being
                // casted to 'array' and if it is already array (i.e. in case of Eloquent Builder), casting does not make any change
                $obj = (array)$obj;
                $obj[$id_to_encode] = $hash_generator->encode($obj[$id_to_encode]);
                $data[$key] = $obj;
            }

        }
        return $data;

    }


    public function getDataFromSessionOrDB($session_key, $callback)
    {
        $session = request()->session();

        // if request is coming from web, get data from session using '$session_key' if available
        if ($session && $data = session($session_key)) {
            //Log::info("1. $session_key");
            return $data;

        } else {
            //Log::info("2. $session_key");
            // always get data from DB, if request is coming from an API or request is coming from web but the data is not already set in session
            $data = $callback();

            if ($session) {
                $session->put($session_key, $data);
            }
            return $data;
        }
    }

}
