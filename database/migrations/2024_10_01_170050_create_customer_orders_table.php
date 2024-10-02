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
                $table->string('image_link')->nullable();
                $table->text('order_details')->nullable(); // Details of the order
                $table->string('order_kind'); // kind of the order
                $table->string('ring_size')->nullable(); 
                $table->string('weight')->nullable();
                $table->string('gold_color')->nullable();
                $table->string('order_fix_type'); // kind of the order fix
                $table->string('customer_name');
                $table->string('seller_name');
                $table->decimal('deposit', 10, 2);
                $table->decimal('rest_of_cost', 10, 2);
                $table->string('customer_phone',11); 
                $table->date('order_date');
                $table->date('deliver_date')->nullable();
                $table->string('status')->nullable();

                // $table->enum('status', ['في المحل', 'في الورشة', 'خلص', 'تم استلامه' ,'في الدمغة'])->default('في المحل'); // Order status
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
