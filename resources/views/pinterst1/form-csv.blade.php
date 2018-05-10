
<div class="panel-heading">Upload Csv</div>
<div class="panel-body">
    
        {{ Form::open(array('url' => route('pinterest1.import.success',['etsyStore'=>$store_name]),'files'=>'true'))}}
        {{ Form::file('csv')}}
        {{ Form::submit('Upload File')}}
        {{ Form::close()}}
     
</div>