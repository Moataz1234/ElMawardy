use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gold_items', function (Blueprint $table) {
            $table->string('talabat')->nullable();
            $table->foreign('talabat')->references('model')->on('talabat')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('gold_items', function (Blueprint $table) {
            $table->dropForeign(['talabat']);
            $table->dropColumn('talabat');
        });
    }
};
