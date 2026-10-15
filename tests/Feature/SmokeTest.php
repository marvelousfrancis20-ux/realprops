<?php

use App\Models\Amenity;
use App\Models\Category;
use App\Models\City;
use App\Models\ContactMessage;
use App\Models\Inquiry;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\Tag;
use App\Models\TourRequest;
use App\Models\User;

/**
 * Agents only "exist" for the public profile once the admin has verified them.
 */
function createVerifiedAgent(array $attributes = []): User
{
    $agent = createAgent($attributes);
    $agent->ensureAgentProfile()->forceFill(['verified_at' => now()])->save();

    return $agent->refresh();
}

it('renders every public page', function () {
    $agent = createVerifiedAgent();
    $property = Property::factory()->forSale()->create(['agent_id' => $agent->id, 'is_approved' => true]);
    $post = Post::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);
    $category = Category::factory()->create();
    $tag = Tag::factory()->create();
    Page::factory()->create(['slug' => 'about', 'title' => 'About', 'content' => '<p>About us</p>', 'is_active' => true]);

    $this->get(route('home'))->assertOk();
    $this->get(route('properties.index'))->assertOk();
    $this->get(route('properties.show', $property))->assertOk();
    $this->get(route('agents.index'))->assertOk();
    $this->get(route('agents.show', $agent))->assertOk();
    $this->get(route('blog.index'))->assertOk();
    $this->get(route('blog.show', $post))->assertOk();
    $this->get(route('blog.category', $category->slug))->assertOk();
    $this->get(route('blog.tag', $tag->slug))->assertOk();
    $this->get(route('about'))->assertOk();
    $this->get(route('faq'))->assertOk();
    $this->get(route('terms'))->assertOk();
    $this->get(route('privacy'))->assertOk();
    $this->get(route('pages.show', 'about'))->assertOk();
    $this->get(route('contact'))->assertOk();
});

it('renders guest auth pages', function () {
    $this->get(route('login'))->assertOk();
    $this->get(route('register'))->assertOk();
    $this->get(route('password.request'))->assertOk();
});

it('renders logged-in user pages', function () {
    $user = createUser();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
    $this->actingAs($user)->get(route('favorites.index'))->assertOk();
    $this->actingAs($user)->get(route('inquiries.index'))->assertOk();
    $this->actingAs($user)->get(route('compare.index'))->assertOk();
    $this->actingAs($user)->get(route('saved-searches.index'))->assertOk();
    $this->actingAs($user)->get(route('profile.edit'))->assertOk();
});

it('renders agent pages', function () {
    $agent = createVerifiedAgent();
    $property = Property::factory()->forSale()->create(['agent_id' => $agent->id, 'is_approved' => true]);
    Plan::factory()->create(['is_active' => true, 'price' => '29.00']);
    $inquiry = Inquiry::factory()->create([
        'property_id' => $property->id,
        'agent_id' => $agent->id,
    ]);
    TourRequest::factory()->create([
        'property_id' => $property->id,
        'agent_id' => $agent->id,
    ]);

    $this->actingAs($agent)->get(route('agent.dashboard'))->assertOk();
    $this->actingAs($agent)->get(route('agent.analytics'))->assertOk();
    $this->actingAs($agent)->get(route('agent.properties.index'))->assertOk();
    $this->actingAs($agent)->get(route('agent.properties.create'))->assertOk();
    $this->actingAs($agent)->get(route('agent.properties.show', $property))->assertOk();
    $this->actingAs($agent)->get(route('agent.properties.edit', $property))->assertOk();
    $this->actingAs($agent)->get(route('agent.inquiries.index'))->assertOk();
    $this->actingAs($agent)->get(route('agent.inquiries.show', $inquiry))->assertOk();
    $this->actingAs($agent)->get(route('agent.tours.index'))->assertOk();
    $this->actingAs($agent)->get(route('agent.subscription.index'))->assertOk();
    $this->actingAs($agent)->get(route('agent.profile.edit'))->assertOk();
});

it('renders admin pages', function () {
    $admin = createAdmin();
    $other = createUser();
    $agent = createVerifiedAgent();
    $property = Property::factory()->forSale()->create(['agent_id' => $agent->id, 'is_approved' => false]);
    $post = Post::factory()->draft()->create(['author_id' => $other->id]);
    $page = Page::factory()->create(['title' => 'Careers', 'slug' => 'careers']);
    $plan = Plan::factory()->create();
    $message = ContactMessage::factory()->create();

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('admin.properties.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.properties.show', $property))->assertOk();
    $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.users.show', $other))->assertOk();
    $this->actingAs($admin)->get(route('admin.agents.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.agents.show', $agent->agentProfile))->assertOk();
    $this->actingAs($admin)->get(route('admin.posts.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.posts.index', ['status' => 'draft']))->assertOk();
    $this->actingAs($admin)->get(route('admin.posts.create'))->assertOk();
    $this->actingAs($admin)->get(route('admin.posts.edit', $post))->assertOk();
    $this->actingAs($admin)->get(route('admin.pages.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.pages.create'))->assertOk();
    $this->actingAs($admin)->get(route('admin.pages.edit', $page))->assertOk();
    $this->actingAs($admin)->get(route('admin.plans.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.plans.create'))->assertOk();
    $this->actingAs($admin)->get(route('admin.plans.edit', $plan))->assertOk();
    $this->actingAs($admin)->get(route('admin.contact-messages.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.contact-messages.show', $message))->assertOk();
    $this->actingAs($admin)->get(route('admin.settings'))->assertOk();
    $this->actingAs($admin)->get(route('admin.reports'))->assertOk();
    $this->actingAs($admin)->get(route('admin.activity-log'))->assertOk();

    $resources = [
        'amenities' => [Amenity::factory(), 'name'],
        'types' => [PropertyType::factory(), 'name'],
        'states' => [State::factory(), 'name'],
        'cities' => [City::factory(), 'name'],
        'categories' => [Category::factory(), 'name'],
        'tags' => [Tag::factory(), 'name'],
    ];

    foreach ($resources as $resource => [$factory, $attribute]) {
        $row = $factory->create();

        $this->actingAs($admin)->get(route("admin.{$resource}.index"))->assertOk();
        $this->actingAs($admin)->get(route("admin.{$resource}.create"))->assertOk();
        $this->actingAs($admin)->get(route("admin.{$resource}.edit", $row->id))->assertOk();
    }
});

it('shows custom error pages', function () {
    $this->get('/nope-not-here')->assertNotFound();

    $this->actingAs(createUser())->get(route('admin.dashboard'))->assertForbidden();
});
