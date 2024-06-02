<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use mysql_xdevapi\Exception;

class LinkController extends Controller
{

    public function index(Request $request){

        $perPage = 10;
        $page = $request->input('page', 1);

        $start = ($page - 1) * $perPage;
        $end = $start + $perPage - 1;
        $redisData = Redis::get(Auth::user()->name, $start ,$end);
        $currentPageItems=[];
        if(!empty($redisData)){
            $redisData=json_decode($redisData,true);
            usort($redisData,'redisSort');
            $currentPageItems= array_slice(($redisData),($page-1)*$perPage,$perPage);
        }

        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            $redisData?count($redisData):0,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return view('link.index', ['linksPaginator' => $paginator]);
    }

    public function createLink()
    {
        return view('link.create');
    }

    public function create(Request $request)
    {
        $linkText='http://'.$request->request->get('text');
        $result =  env('APP_URL', 'localhost').'/'.hash_string($linkText,5);
        $redisValueArray=Redis::get(Auth::user()->name);

        $datas = [
            ['Text' => $linkText, 'Link' => $result, 'Click' => 0]
        ];

        if(!empty($redisValueArray) && isset($redisValueArray))
            $datas= array_merge($datas,json_decode($redisValueArray,true));

        Redis::set(Auth::user()->name,json_encode($datas));
          return view('link.create');
    }


    public function updateRedisValue(Request $request)
    {
        $request->validate([
            'link' => 'required|string',
        ]);

        $link = $request->input('link');
        $oldValueJson = Redis::get(Auth::user()->name);
        $oldValue = $oldValueJson ? json_decode($oldValueJson, true) : [];

        foreach ($oldValue as &$item){
            if(isset($item['Link'])  && $item['Link'] == $link ){
                $item['Click']++;
                break;
            }
        }

        Redis::set(Auth::user()->name, json_encode($oldValue));
        return response()->json(['success' => true]);
    }


    public function getLinksCount(int $count=null){
        $redisValueArray=Redis::get(Auth::user()->name);
        $links=[];
        if(!empty($redisValueArray)){
            $redisValueArray=json_decode($redisValueArray,true);
            usort($redisValueArray,'redisSort');
            $links= array_slice(($redisValueArray),0,isset($count)?$count:count($redisValueArray));
        }
        return response()->json(['links' => $links]);
    }

    public function search(string $link){

        $linkValues =Redis::get(Auth::user()->name);
        $linkValues = $linkValues ? json_decode($linkValues, true) : [];
        $result=[];
        foreach ($linkValues as $item){
            if(isset($item['Text']) ){;
               if(str_contains($item['Text'],$link)){
                   array_push($result,$item);
               }
            }
        }
        return response()->json(['links' => $result]);
    }


}
