<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMotorCycleRequest;
use App\Http\Requests\UpdateMotorCycleRequest;
use App\Models\_Model;
use App\Models\Maker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Motorcycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MotorcycleController extends Controller
{

    public function index(Request $request)
    {
        $query = Motorcycle::with(['maker', 'model'])
            ->filterMaker($request->maker)
            ->filterModel($request->model)
            ->filterNensiki($request->nensiki)
            ->filterMaxOdo($request->max_odo)
            ->filterMaxPrice($request->max_price)
            ->sortByField($request->sort_by, $request->sort_order);

        return $query->paginate($request->per_page ?? 10);
    }
    public function create()
    {
        return Inertia::render('MotorcycleCreate');
    }

    public function store(StoreMotorCycleRequest $request)
    {
        $data = $request->all();
        $this->handleMotorcycleImages($data['images'] ?? [], $data);
        return Motorcycle::create($data);
    }

    public function show($id)
    {
        return Motorcycle::findOrFail($id);
    }

    public function update(UpdateMotorCycleRequest $request, $id)
    {
        $motorcycle = Motorcycle::findOrFail($id);
        $data = $request->all();
        $this->handleMotorcycleImagesUpdate($data['images'] ?? [], $data, $motorcycle);
        $motorcycle->update($data);
        return $motorcycle;
    }

    public function destroy($id)
    {
        $motorcycle = Motorcycle::findOrFail($id);
        $motorcycle->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function clone($id)
    {
        $original = Motorcycle::findOrFail($id);
        $copy = $original->replicate();

        for ($i = 1; $i <= 10; $i++) {
            $photoKey = "photo$i";
            $titleKey = "photo{$i}_pr";

            if ($original->$photoKey) {
                $originalPath = $original->$photoKey;
                $extension = pathinfo($originalPath, PATHINFO_EXTENSION) ?: 'jpg';
                $newPath = "motorcycles/photos/clone_" . uniqid() . ".$extension";

                Storage::disk('public')->copy($originalPath, $newPath);

                $copy->$photoKey = $newPath;
                $copy->$titleKey = $original->$titleKey;
            }
        }

        $copy->save();

        return $copy;
    }
    public function postMotorcycle(Request $request, $id)
    {
        $motorcycle = Motorcycle::findOrFail($id);
        $current = $motorcycle->iyoukyo;
        $next = $request->input('status');
        $allowedStatuses = [0, 1, 2];

        if (!in_array($next, $allowedStatuses)) {
            return response()->json(['error' => 'Trạng thái không hợp lệ.'], 422);
        }
        $transitions = [
            0 => 1, // ẩn → bán
            1 => 2, // bán → đăng
            2 => 0, // đăng → ẩn
        ];
        if (!isset($transitions[$current]) || $transitions[$current] !== $next) {
            return response()->json(['error' => "Không thể chuyển trạng thái"], 422);
        }
        if ($current === 1 && $next === 2) {
            $requiredFields = [
                'maker_code',
                'model_code',
                'ippan_kakaku',
                'nensiki',
                'soukou',
                'haikiryo',
                'color',
                'iyoukyo',
                'noridasi_kakaku',
                'photo1',
            ];

            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (empty($motorcycle->$field)) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                return response()->json([
                    'error' => 'Thiếu thông tin để đăng xe.',
                    'missing_fields' => $missingFields
                ], 422);
            }
        }
        $motorcycle->iyoukyo = $next;
        $motorcycle->save();

        return response()->json([
            'message' => "Trạng thái đã chuyển trạng thái thành công.",
            'new_status' => $next
        ]);
    }
    public function bulkUpdate(Request $request)
    {
        $data = $request->all();

        $data['fields'] = array_filter($data['fields'], function ($value) {
            return !is_null($value);
        });
        Motorcycle::whereIn('id', $data['ids'])
            ->update($data['fields']);

        return response()->json([
            'message' => "Cập nhật  xe thành công.",
        ]);
    }

    public function makerSelect()
    {
        return   Maker::all();
    }
    public function modelSelect()
    {
        return _Model::all();
    }


    protected function handleMotorcycleImages(array $images, array &$updateData, Motorcycle $motorcycle = null): void
    {
        for ($i = 0; $i < 10; $i++) {
            $photoKey = 'photo' . ($i + 1);
            $titleKey = $photoKey . '_pr';
            $updateData[$photoKey] = null;
            $updateData[$titleKey] = null;
        }

        foreach ($images as $index => $imageData) {
            if ($index >= 10) break;

            $photoKey = 'photo' . ($index + 1);
            $titleKey = $photoKey . '_pr';

            if (isset($imageData['images']) && $imageData['images'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $imageData['images']->store('motorcycles/photos', 'public');
                $updateData[$photoKey] = $path;
                $updateData[$titleKey] = $imageData['title'] ?? '';
            } elseif ($motorcycle && $motorcycle->{$photoKey}) {
                $updateData[$photoKey] = $motorcycle->{$photoKey};
                $updateData[$titleKey] = $motorcycle->{$titleKey};
            }
        }
    }

    protected function handleMotorcycleImagesUpdate(array $images, array &$updateData, Motorcycle $motorcycle): void
    {
        $newSlots = [];

        foreach ($images as $imageData) {
            $slot = (int) ($imageData['slot'] ?? 0);
            if ($slot < 1 || $slot > 10) continue;

            $photoKey = "photo{$slot}";
            $titleKey = "{$photoKey}_pr";
            $newSlots[] = $photoKey;

            if (!empty($imageData['is_deleted'])) {
                if (!empty($motorcycle->{$photoKey})) {
                    Storage::disk('public')->delete($motorcycle->{$photoKey});
                }
                $updateData[$photoKey] = null;
                $updateData[$titleKey] = null;
                continue;
            }

            if (!empty($imageData['is_new']) && $imageData['images'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $imageData['images'];

                $extension = strtolower($file->getClientOriginalExtension());
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'jfif'];

                if (!in_array($extension, $allowedExtensions)) {
                    throw ValidationException::withMessages([
                        "images.{$slot}" => "Ảnh slot {$slot} phải là định dạng jpg, jpeg, png hoặc webp.",
                    ]);
                }

              
                if ($file->getSize() >  1024 * 70) {

                    throw ValidationException::withMessages([
                        "images.{$slot}" => "Ảnh slot {$slot} vượt quá kích thước tối đa 70MB.",
                    ]);
                }

                if (!empty($motorcycle->{$photoKey})) {
                    Storage::disk('public')->delete($motorcycle->{$photoKey});
                }

                $path = $file->store('motorcycles/photos', 'public');
                $updateData[$photoKey] = $path;
                $updateData[$titleKey] = $imageData['title'] ?? '';
                continue;
            }

            if (is_string($imageData['images'])) {
                $updateData[$photoKey] = $imageData['images'];
                $updateData[$titleKey] = 'Ảnh' . $slot;
            }
        }

        for ($i = 1; $i <= 10; $i++) {
            $photoKey = "photo{$i}";
            $titleKey = "{$photoKey}_pr";
            if (!in_array($photoKey, $newSlots)) {
                if (!empty($motorcycle->{$photoKey})) {
                    Storage::disk('public')->delete($motorcycle->{$photoKey});
                }
                $updateData[$photoKey] = null;
                $updateData[$titleKey] = null;
            }
        }
    }
}
