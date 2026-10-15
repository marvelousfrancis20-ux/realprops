<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Category;
use App\Models\City;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Generic CRUD for master data (amenities, property types, cities, states, categories, tags).
 * Each resource is described by a small config array so the UI stays consistent.
 */
class MasterDataController extends Controller
{
    /**
     * @var array<string, array<string, mixed>>
     */
    public const RESOURCES = [
        'amenities' => [
            'model' => Amenity::class,
            'label' => 'Amenity',
            'columns' => ['name' => 'Name', 'slug' => 'Slug', 'icon' => 'Icon', 'is_active' => 'Active'],
            'fields' => [
                'name' => ['label' => 'Name', 'type' => 'text', 'required' => true],
                'icon' => ['label' => 'Icon (SVG / emoji)', 'type' => 'text'],
                'is_active' => ['label' => 'Visible to visitors', 'type' => 'checkbox', 'default' => true],
            ],
        ],
        'types' => [
            'model' => PropertyType::class,
            'label' => 'Property Type',
            'columns' => ['name' => 'Name', 'slug' => 'Slug', 'sort_order' => 'Order', 'is_active' => 'Active'],
            'fields' => [
                'name' => ['label' => 'Name', 'type' => 'text', 'required' => true],
                'description' => ['label' => 'Description', 'type' => 'text'],
                'icon' => ['label' => 'Icon (SVG / emoji)', 'type' => 'text'],
                'sort_order' => ['label' => 'Sort order', 'type' => 'number', 'default' => 0],
                'is_active' => ['label' => 'Visible to visitors', 'type' => 'checkbox', 'default' => true],
            ],
        ],
        'states' => [
            'model' => State::class,
            'label' => 'State',
            'columns' => ['name' => 'Name', 'slug' => 'Slug', 'code' => 'Code', 'country' => 'Country', 'is_active' => 'Active'],
            'fields' => [
                'name' => ['label' => 'Name', 'type' => 'text', 'required' => true],
                'code' => ['label' => 'Code', 'type' => 'text'],
                'country' => ['label' => 'Country', 'type' => 'text', 'default' => 'Nigeria'],
                'is_active' => ['label' => 'Active', 'type' => 'checkbox', 'default' => true],
            ],
        ],
        'cities' => [
            'model' => City::class,
            'label' => 'City',
            'columns' => ['name' => 'Name', 'state' => 'State', 'slug' => 'Slug', 'is_active' => 'Active'],
            'fields' => [
                'state_id' => ['label' => 'State', 'type' => 'select', 'required' => true, 'options_model' => State::class],
                'name' => ['label' => 'Name', 'type' => 'text', 'required' => true],
                'latitude' => ['label' => 'Latitude', 'type' => 'text'],
                'longitude' => ['label' => 'Longitude', 'type' => 'text'],
                'is_active' => ['label' => 'Active', 'type' => 'checkbox', 'default' => true],
            ],
        ],
        'categories' => [
            'model' => Category::class,
            'label' => 'Category',
            'columns' => ['name' => 'Name', 'slug' => 'Slug', 'posts_count' => 'Posts'],
            'fields' => [
                'name' => ['label' => 'Name', 'type' => 'text', 'required' => true],
                'description' => ['label' => 'Description', 'type' => 'text'],
            ],
        ],
        'tags' => [
            'model' => Tag::class,
            'label' => 'Tag',
            'columns' => ['name' => 'Name', 'slug' => 'Slug', 'posts_count' => 'Posts'],
            'fields' => [
                'name' => ['label' => 'Name', 'type' => 'text', 'required' => true],
            ],
        ],
    ];

    /**
     * @return array<string, mixed>
     */
    private function config(string $resource): array
    {
        abort_unless(isset(self::RESOURCES[$resource]), 404);

        return self::RESOURCES[$resource];
    }

    public function index(Request $request, string $resource): View
    {
        $config = $this->config($resource);
        $modelClass = $config['model'];

        /** @var Builder $query */
        $query = $modelClass::query();

        if (filled($request->query('q'))) {
            $q = $request->query('q');
            $query->where(fn ($inner) => $inner
                ->where('name', 'like', '%'.$q.'%')
                ->orWhere('slug', 'like', '%'.$q.'%'));
        }

        if (in_array('posts_count', $config['columns'], true)) {
            $query->withCount('posts');
        }

        if ($resource === 'cities') {
            $query->with('state');
        }

        $this->authorize('viewAny', $modelClass);

        return view('admin.master-data.index', [
            'resource' => $resource,
            'config' => $config,
            'rows' => $query->orderBy('name')->paginate(20)->withQueryString(),
        ]);
    }

    public function create(string $resource): View
    {
        $config = $this->config($resource);

        $this->authorize('create', $config['model']);

        return view('admin.master-data.form', [
            'resource' => $resource,
            'config' => $config,
            'options' => $this->fieldOptions($config),
            'row' => new $config['model'],
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $config = $this->config($resource);

        $this->authorize('create', $config['model']);

        $row = new $config['model'];

        $row->fill($this->validated($request, $config));
        $row->save();

        return redirect()
            ->route("admin.{$resource}.index")
            ->with('status', $config['label'].' created.');
    }

    public function edit(int $id, string $resource): View
    {
        $config = $this->config($resource);

        /** @var Model $row */
        $row = $config['model']::findOrFail($id);

        $this->authorize('update', $row);

        return view('admin.master-data.form', [
            'resource' => $resource,
            'config' => $config,
            'options' => $this->fieldOptions($config),
            'row' => $row,
        ]);
    }

    /**
     * Resolve the option lists for select fields of a resource config.
     *
     * @param  array<string, mixed>  $config
     * @return array<string, Collection>
     */
    private function fieldOptions(array $config): array
    {
        $options = [];

        foreach ($config['fields'] as $key => $field) {
            if (($field['type'] ?? null) === 'select') {
                $related = new $field['options_model'];
                $options[$key] = $related::query()->orderBy('name')->pluck('name', 'id');
            }
        }

        return $options;
    }

    public function update(Request $request, int $id, string $resource): RedirectResponse
    {
        $config = $this->config($resource);

        /** @var Model $row */
        $row = $config['model']::findOrFail($id);

        $this->authorize('update', $row);

        $row->fill($this->validated($request, $config));
        $row->save();

        return redirect()
            ->route("admin.{$resource}.index")
            ->with('status', $config['label'].' updated.');
    }

    public function destroy(int $id, string $resource): RedirectResponse
    {
        $config = $this->config($resource);

        /** @var Model $row */
        $row = $config['model']::findOrFail($id);

        $this->authorize('delete', $row);

        $row->delete();

        return redirect()
            ->route("admin.{$resource}.index")
            ->with('status', $config['label'].' deleted.');
    }

    /**
     * Build validation rules from the resource config and normalise the validated data.
     *
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function validated(Request $request, array $config): array
    {
        $rules = [];
        $attributes = [];

        foreach ($config['fields'] as $key => $field) {
            $type = $field['type'];

            $rules[$key] = match ($type) {
                'checkbox' => ['sometimes', 'boolean'],
                'number' => ['sometimes', 'integer', 'min:-1000000', 'max:1000000'],
                'select' => ['required', 'integer', 'exists:'.(new $field['options_model'])->getTable().',id'],
                default => array_values(array_filter([
                    isset($field['required']) && $field['required'] ? 'required' : 'nullable',
                    'string',
                    'max:255',
                ])),
            };

            $attributes[$key] = $field['label'];
        }

        $validated = $request->validate($rules, [], $attributes);

        foreach ($config['fields'] as $key => $field) {
            if ($field['type'] === 'checkbox') {
                $validated[$key] = (bool) ($validated[$key] ?? ($field['default'] ?? false));
            }

            if ($field['type'] === 'number' && isset($validated[$key]) && $validated[$key] !== null) {
                $validated[$key] = (int) $validated[$key];
            }
        }

        return $validated;
    }
}
