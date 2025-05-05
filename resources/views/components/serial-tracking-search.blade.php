<div class="serial-tracking-widget">
    <h5 class="widget-title"><i class="fas fa-search"></i> Serial Number Tracking</h5>
    <form action="{{ route('tracking.search') }}" method="GET" class="d-flex">
        <div class="input-group">
            <input type="text" class="form-control" name="serial_number" 
                placeholder="Enter serial number" required>
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search"></i> Track
            </button>
        </div>
    </form>
</div>

<style>
.serial-tracking-widget {
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    margin-bottom: 20px;
}
.widget-title {
    margin-bottom: 15px;
    color: #0d6efd;
    font-weight: 600;
}
</style> 