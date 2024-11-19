    
<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Transfer Requests</title>
    <style>
       body
       {
          font-family: 'Roboto', sans-serif;
          background-color: #ffffff;
        }
    
        .container {
          /* width: 100%; */
          /* max-width: 1000px; */
          padding: 20px;
          margin-left: 300px;
        }
    
        .requests-list {
          background-color: #FFFFFF;
          padding: 20px;
          border-radius: 0 0 8px 8px;
          box-shadow: 0px 0px 8px rgba(0,0,0,0.1);
        }
    
        .request-card {
          background-color: #002855;
          color: #FFFFFF;
          padding: 15px;
          border-radius: 8px;
          margin-bottom: 15px;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }
    
        .request-info {
          flex-grow: 1;
        }
    
        .request-id {
          font-weight: 700;
          font-size: 18px;
          margin: 0 0 8px;
        }
    
        .request-details {
          background-color: #D8D8D8;
          padding: 5px 10px;
          border-radius: 5px;
          display: inline-block;
          color: #333;
          font-size: 14px;
          font-weight: 600;
        }
    
        .request-buttons {
          display: flex;
          gap: 10px;
        }
    
        .btn-reject {
          background-color: #D32F2F;
          color: #FFFFFF;
          padding: 8px 15px;
          border: none;
          border-radius: 5px;
          font-weight: 600;
          cursor: pointer;
          font-size: 14px;
        }
    
        .btn-accept {
          background-color: #4CAF50;
          color: #FFFFFF;
          padding: 8px 15px;
          border: none;
          border-radius: 5px;
          font-weight: 600;
          cursor: pointer;
          font-size: 14px;
          text-decoration: none;
        }
/*     
        .btn-reject:hover {
          background-color: #B71C1C;
        } */
    
        .btn-accept:hover {
          background-color: #388E3C;
        }
      </style>
</head>
<body >
    {{-- <div class="body-requests"> --}}
    <div class="container">
          <h1>Requests</h1>
     
    @foreach($transferRequests as $request)
    <div class="request-details">
        <div class="request-card">
            <div class="request-info">
        <p class="request-id"> {{ $request->goldItem->serial_number }}</p>

        <div class="request-details">
            <p><strong>From Shop:</strong>{{ $request->from_shop_name }}</p> 
        </div>
        <div class="request-details">
            <p><strong>To Shop:</strong> {{ $request->to_shop_name }}</p>
        </div>
          <div class="request-details">
            <p><strong>Status:</strong> {{ $request->status }}</p>
        </div>
      
        @if($request->status == 'pending')
            <a class="btn-accept" href="{{ route('transfer.handle', ['id' => $request->id, 'status' => 'accepted']) }}">Accept</a>
            {{-- <a href="{{ route('transfer.handle', ['id' => $request->id, 'status' => 'rejected']) }}">Reject</a> --}}
        @endif
    </div>
</div>
        <hr>
    </div>
    @endforeach
</div>
    </div>
</body>
</html>
