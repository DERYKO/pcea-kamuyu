<?php

namespace App\Http\Controllers;

use App\Data\Models\Quote;
use App\Http\Controllers\Reports\ProductsReport;
use App\Http\Controllers\Transformers\QuoteTransformer;
use App\Jobs\EmailProductReport;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    use Helpers;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotes = Quote::orderByDesc('created_at')->paginate(50);
        return $this->response->paginator($quotes, new QuoteTransformer());

    }

    public function download(){
        (new ProductsReport())->store('quotes.xlsx')->chain([
           new EmailProductReport(storage_path().'/app/quotes.xlsx')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'quote' => ['required']
        ]);
        $quote = Quote::create([
            'quote' => $request->quote,
            'uploaded_by' => $request->user()->id
        ]);
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);
        $quote->update($request->only('quote'));
        return $this->index();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();
        return $this->index();
    }
}
