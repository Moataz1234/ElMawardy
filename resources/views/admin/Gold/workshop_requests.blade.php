<head>
    @include('components.navbar')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Workshop Transfer Requests</title>
    <style>
       body {
          font-family: 'Roboto', sans-serif;
          background-color: #ffffff;
        }
    
        .container {
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
    
        .btn-accept:hover {
          background-color: #388E3C;
        }
    </style>
</head>
<body>
    <h1><center>Workshop Transfer Requests</center></h1>

    <div class="container">
        @foreach($requests as $request)
        <div class="request-details">
            <div class="request-card">
                <div class="request-info">
                    <p class="request-id">{{ $request->serial_number }}</p>
                    
                    <div class="request-details">
                        <p><strong>Shop:</strong> {{ $request->shop_name }}</p>
                    </div>
                    <div class="request-details">
                        <p><strong>Requested By:</strong> {{ $request->requested_by }}</p>
                    </div>
                    <div class="request-details">
                        <p><strong>Reason:</strong> {{ $request->reason }}</p>
                    </div>
                    <div class="request-details">
                        <p><strong>Status:</strong> 
                            <span style="color: {{ $request->status === 'pending' ? '#FFA500' : ($request->status === 'approved' ? '#4CAF50' : '#D32F2F') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </p>
                    </div>
                    
                    @if($request->status == 'pending')
                        <div class="request-buttons">
                            <form method="POST" action="{{ route('workshop.requests.handle', $request->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn-accept">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('workshop.requests.handle', $request->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn-reject">Reject</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            <hr>
        </div>
        @endforeach
    </div>
    {{ $requests->links() }}
</body>
</html>
