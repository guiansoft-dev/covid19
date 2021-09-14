<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

class ConsumirApiController extends Controller
{
    //
    public function index(Request $request)
    {

        $state = $request->get('state');
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        $key = ('Token b63ac5281e200ab9ce01cbbd9b2b5b1713129560');
        $responseStart = ("https://api.brasil.io/v1/dataset/covid19/caso/data/?state={$state}&date={$dateStart}");
        $responseEnd = ("https://api.brasil.io/v1/dataset/covid19/caso/data/?state={$state}&date={$dateEnd}");

        $url = ('https://us-central1-lms-nuvem-mestra.cloudfunctions.net/testApi');
        $MeuNome = ('Jonathas Alves Santos');

        $response = Http::withHeaders(['Authorization' => $key])->get($responseStart, $responseEnd);

        $collection = collect($response['results']);

        $filtered = $collection->filter(function ($value, $key) {
            return $value['city'] > null;
        });

        $sortByDesc = $filtered->sortByDesc(function ($value, $key) {
            return $value['estimated_population'] . $value['confirmed_per_100k_inhabitants'];
        });

        $sorted = $sortByDesc->slice(0, 10);

        $sorted->values()->all();

        foreach ($sorted as $value) {

            $res = Http::withHeaders(
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Jonathas',
                    ]])
                ->post('https://us-central1-lms-nuvem-mestra.cloudfunctions.net/testApi',
                    [
                        'form_params' => [
                            'id' => [0],
                            'nomeCidade' => $value['city'],
                            'percentualDeCasos' => $value['confirmed_per_100k_inhabitants'],
                        ]]);

            //$res = json_decode($res->getBody()->getContents(), true);

            $collection = collect($res);

            dd($collection);

            //dd($collection->throw());
        }

    }
}
