<?php
namespace App\Services\MotorCycle;

use App\Filters\MotorCycle\ByModel;
use App\Filters\MotorCycle\ByOdo;
use App\Filters\MotorCycle\ByYear;
use App\Models\MotorCycle;
use App\QueryFilters\SortBy;
use Backend\App\Filters\MotorCycle\ByMaker;
use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class MotorCycleService
{
    public function getListMotorCycles()
    {
        $pipelines = [
            // ByMaker::class,
            ByModel::class,
            ByYear::class,
            ByOdo::class,
            SortBy::class
        ];

        $query = app(Pipeline::class)
            ->send(MotorCycle::query())
            ->through($pipelines)
            ->thenReturn();

        $motorCycles = $query->with('maker', 'model')->paginate(10);

        return $motorCycles;
    }
    public function storeMotorCycle(array $data)
    {
        $images = $data['images'] ?? [];
        unset($data['images']);
// dd($images);
        $this->handleMotorcycleImages($images, $data);
dd($data);
        return MotorCycle::create($data);
    }
    public function deleteMotorCycle(MotorCycle $motorCycle)
    {
        $motorCycle->delete();
        // return response()->json(['message' => 'Motorcycle deleted successfully'], 200);
    }
    public function getMotorCycleById($id)
    {
        return MotorCycle::with('maker', 'model')->findOrFail($id);
    }
    public function update(MotorCycle $motorcycle, array $data)
    {
        $images = $data['images'] ?? [];
        // dd($images);
        // unset($data['images']);
// dd($data);
        $this->handleMotorcycleImages($images, $data);
dd($data);
        $motorcycle->update($data);
        return $motorcycle;
    }

    // public function handleMotorcycleImages(array $images, array &$updateData): void
    // {
    //     for ($i = 0; $i < 10; $i++) {
    //         $photoKey = "photo" . ($i + 1);
    //         $titleKey = $photoKey . "_pr";

    //         $imageData = $images[$i] ?? null;

    //         if (!$imageData || !empty($imageData['is_deleted'])) {
    //             $updateData[$photoKey] = null;
    //             $updateData[$titleKey] = null;
    //             continue;
    //         }

    //         // Nếu là ảnh mới upload
    //         if (!empty($imageData['is_new']) && isset($imageData['file']) && $imageData['file'] instanceof \Illuminate\Http\UploadedFile) {
    //             $path = $imageData['file']->store('motorcycles/photos', 'public');
    //             $updateData[$photoKey] = $path;
    //         } elseif (!empty($imageData['path'])) {
    //             // Ảnh cũ giữ nguyên
    //             $updateData[$photoKey] = $imageData['path'];
    //         } else {
    //             $updateData[$photoKey] = null;
    //         }

    //         $updateData[$titleKey] = $imageData['title'] ?? null;
    //     }
    // }
    public function handleMotorcycleImages(array $images, array &$updateData): void
{
    for ($i = 0; $i < 10; $i++) {
        $photoKey = "photo" . ($i + 1);
        $titleKey = $photoKey . "_pr";

        if (!isset($images[$i])) {
            $updateData[$photoKey] = null;
            $updateData[$titleKey] = null;
            continue;
        }

        $imagesPath = $images[$i];

        if ($imagesPath instanceof UploadedFile) {
             $fileName = Str::slug(pathinfo($imagesPath->getClientOriginalName(), PATHINFO_FILENAME))
            . '_' . time() . '.' . $imagesPath->getClientOriginalExtension();
            // dd($fileName);
            $imagesPath = $imagesPath->move(public_path('motorcycles/photos'),  $fileName);
            // dd( $path );
            $updateData[$photoKey] = $imagesPath;
            $updateData[$titleKey] = $fileName; // Nếu chưa có title
        } else {
            $updateData[$photoKey] = null;
            $updateData[$titleKey] = null;
        }
    }
}

}
