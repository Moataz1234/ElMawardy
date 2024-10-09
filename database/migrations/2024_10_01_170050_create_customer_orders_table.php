    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade'); // Linking shop with order
                $table->string('order_number')->nullable();
                $table->text('order_details')->nullable(); // Details of the order
                $table->string('customer_name')->nullable();
                $table->string('seller_name')->nullable();
                $table->decimal('deposit', 10, 2)->nullable();
                $table->decimal('rest_of_cost', 10, 2)->nullable();
                $table->string('payment_method')->nullable();
                $table->string('customer_phone',11)->nullable(); 
                $table->date('order_date')->nullable();
                $table->date('deliver_date')->nullable();
                $table->string('status')->nullable();
                $table->string('by_customer')->nullable();
                $table->string('by_Shop')->nullable();
                $table->string('by_two')->nullable();
                $table->timestamps();
            });
        }
        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('orders');
        }
    };
