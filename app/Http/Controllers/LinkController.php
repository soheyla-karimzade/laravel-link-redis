<?php

namespace App\Http\Controllers;

use App\Services\LinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{

    protected LinkService $linkService;

    public function __construct(LinkService $linkService)
    {
        $this->linkService = $linkService;
    }

    public function index(Request $request){
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $links = $this->linkService->paginateLinks($perPage);
        return view('link.index', compact('links'));
    }

    public function create()
    {
        return view('link.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'text' => 'required',
        ]);

        $linkText='http://'.$request->request->get('text');
        $result =  env('APP_URL', 'localhost').'/'.hash_string($linkText,5);

        $data = [
            'text' => $linkText, 'link' => $result, 'click' => 0,'user_id'=>Auth::id()
        ];

        $this->linkService->createLink($data);
        return view('link.create');
    }


    public function updateRedisValue(Request $request)
    {
        $request->validate([
            'link' => 'required|string',
        ]);
        $id=$request->request->get('id');
        $link = $this->linkService->getLinkById($id);
        $this->linkService->updateLink($id,['click'=>$link->click+1]);
        return response()->json(['success' => true]);
    }


    public function getLinksCount(int $count=null){

        $links = $this->linkService->getAllLinks();
        $values = json_decode($links ,true);
        if(!empty($values)){
            usort($values,'redisSort');
            $links= array_slice(($values),0,isset($count)?$count:count($values));
        }
        return response()->json(['links' => $links]);
    }

    public function search(string $link){
        $result=[];
        $links = $this->linkService->getAllLinks();
        $values = json_decode($links ,true);

        foreach ($values as $value) {

            if(isset($value['text']) ){
                if(str_contains($value['text'],$link)){
                    array_push($result,$value);
                }
            }
        }
        return response()->json(['links' => $result]);
    }


}
