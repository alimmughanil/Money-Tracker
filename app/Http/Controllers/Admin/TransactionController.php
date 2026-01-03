<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ActionType;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Http\Controllers\Core\BaseResourceController;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends BaseResourceController
{
  protected $model = Transaction::class;

  protected function indexQuery($query, $request)
  {
    $query->filterRole()->with(['user', 'category']);
    return $query;
  }
  public function webhook(Request $request, $phone)
  {
    $user = User::where('phone', $phone)->first();
    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    $data = $request->all();

    DB::beginTransaction();
    try {
      $amount = isset($data['Total']) ? (int) preg_replace('/[^0-9]/', '', $data['Total']) : 0;
      $date = $data['Tanggal Struk'] ?? $data['Tanggal Input'] ?? date('Y-m-d');

      Transaction::create([
        'user_id' => $user->id,
        'category_id' => null, // Needs manual categorization later
        'type' => TransactionType::Expense,
        'amount' => $amount,
        'date' => $date,
        'shop_name' => $data['Toko'] ?? null,
        'description' => $data['Items'],
      ]);
      DB::commit();
      return response()->json(['message' => 'Data imported successfully']);
    } catch (\Throwable $th) {
      DB::rollBack();
      Log::error("Webhook Error: " . $th->getMessage());
      return response()->json(['message' => 'Failed to import data', 'error' => $th->getMessage()], 500);
    }
  }

  protected function validation(Request $request, $id = null): array
  {
    return [
      "validation" => [
        "date" => "required|date",
        "type" => "required|in:" . implode(',', TransactionType::getValues()),
        "category_id" => "nullable",
        "amount" => "required|numeric",
        "description" => "nullable|string",
        "shop_name" => "nullable|string",
        "items" => "nullable|array"
      ],
      "default" => [
        "type" => TransactionType::Expense
      ],
    ];
  }

  protected function getPage($request, $id = null): array
  {
    return [
      "label" => "Transaksi",
      "name" => "transaction",
      "url" => "/admin/transactions",
      "inertia" => "Transaction",
      "fields" => \App\Utils\Helper::getFormFields($this->validation($request)),
    ];
  }

  protected function getFormData(Request $request, $model = null): array
  {
    return [
      "page" => $this->page,
      "isAdmin" => $this->isAdmin,
      "typeOptions" => TransactionType::getValues(),
      "categoryOptions" => Category::all()->map(function ($category) {
        return [
          "label" => $category->name,
          "value" => $category->id
        ];
      }),
    ];
  }

  protected function beforeSave(array $validatedData, Request $request): array
  {
    $validatedData['user_id'] = auth()->id();
    return $validatedData;
  }

  protected function beforeActionPage(Request $request, $action = ActionType::Read)
  {
    if (in_array($action, [ActionType::Create, ActionType::Edit])) {
      $user = auth()->user();
      if (empty($user->phone)) {
        return redirect("/app/profile")->with('error', 'Silahkan lengkapi profil terlebih dahulu');
      }
    }
    return null;
  }
  protected function indexValidation(Request $request)
  {
    return $this->beforeActionPage($request, ActionType::Read);
  }
}
