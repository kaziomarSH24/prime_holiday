<?php

namespace App\Http\Controllers;

use App\Models\Continent;
use App\Models\Country;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TravelController extends Controller
{
    /**
     * Cotinent Methods
     */
    //get all continents
    public function getContinents()
    {
        $continents = Continent::all();
        if (!$continents) {
            return response()->json([
                'success' => false,
                'message' => 'No continents found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'continents' => $continents
        ], 200);
    }
    //store continent
    public function storeContinent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:continents',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        $continent = new Continent();
        $continent->name = $request->name;
        $continent->save();
        return response()->json([
            'success' => true,
            'message' => 'Continent created successfully',
            'continent' => $continent
        ], 201);
    }

    //delete continent
    public function deleteContinent($id)
    {
        $continent = Continent::find($id);
        if (!$continent) {
            return response()->json([
                'success' => false,
                'message' => 'Continent not found'
            ], 404);
        }
        $continent->delete();
        return response()->json([
            'success' => true,
            'message' => 'Continent deleted successfully'
        ], 200);
    }


    /**
     * Country Methods
     */

    //get all countries
    public function getCountries(Request $request)
    {
        $countries = Country::orderBy('name')->paginate($request->per_page ?? 10);
        if (!$countries) {
            return response()->json([
                'success' => false,
                'message' => 'No countries found'
            ], 404);
        }

        $countries->transform(function ($country) {
            $imagePath = parse_url($country->image);
            if (isset($imagePath['path'])) {
                $country->image = url($imagePath['path']);
            }
            return [
                'id' => $country->id,
                'continent_id' => $country->continent_id,
                'continent' => $country->continent->name,
                'name' => $country->name,
                'title' => $country->title,
                'image' => $country->image,
                'created_at' => $country->created_at,
                'updated_at' => $country->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'countries' => $countries
        ], 200);
    }

    //show country
    public function getCountry($id)
    {
        $country = Country::where('id', $id)->first();

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }
        $imagePath = parse_url($country->image);
        if (isset($imagePath['path'])) {
            $country->image = url($imagePath['path']);
        }
        $country = [
            'id' => $country->id,
            'continent_id' => $country->continent_id,
            'continent' => $country->continent->name,
            'name' => $country->name,
            'title' => $country->title,
            'image' => $country->image,
            'created_at' => $country->created_at,
            'updated_at' => $country->updated_at
        ];
        return response()->json([
            'success' => true,
            'country' => $country
        ], 200);
    }

    //store country
    public function storeCountry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'continent_id' => 'required|exists:continents,id',
            'name' => 'required|string|unique:countries',
            'title' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'image' => 'required|image|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        //check image file
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . rand(100, 999) . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/images/countries');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $image->move($uploadPath, $imageName);
            $imageUrl = url('uploads/images/countries/' . $imageName);

        }
        $country = new Country();
        $country->continent_id = $request->continent_id;
        $country->name = $request->name;
        $country->title = $request->title;
        $country->image = $imageUrl;
        $country->save();
        return response()->json([
            'success' => true,
            'message' => 'Country created successfully',
            'country' => $country
        ], 201);
    }

    //update country
    public function updateCountry(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'continent_id' => 'required|exists:continents,id',
            'name' => 'required|string|unique:countries,name,' . $id,
            'title' => 'required|string',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        $country = Country::find($id);
        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }




        //check image file
        if ($request->hasFile('image')) {
            //delete old image
            $oldImagePath = parse_url($country->image);
            if (isset($oldImagePath['path']) && file_exists(public_path($oldImagePath['path']))) {
                unlink(public_path($oldImagePath['path']));
            }

            $image = $request->file('image');
            $imageName = time() . rand(100, 999) . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/images/countries/');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $image->move($uploadPath, $imageName);
            $imageUrl = url('uploads/images/countries/' . $imageName);
            $imagePath = parse_url($country->image);
            if (isset($imagePath['path'])) {
                $country->image = url($imagePath['path']);
            }
            $country->image = $imageUrl;
        }
        $country->continent_id = $request->continent_id;
        $country->name = $request->name;
        $country->title = $request->title;
        $country->save();
        return response()->json([
            'success' => true,
            'message' => 'Country updated successfully',
            'country' => $country
        ], 200);
    }

    //delete country
    public function deleteCountry($id)
    {
        $country = Country::find($id);
        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }
        //delete image
        $imagePath = parse_url($country->image);
        if (isset($imagePath['path']) && file_exists(public_path($imagePath['path']))) {
            unlink(public_path($imagePath['path']));
        }

        $country->delete();
        return response()->json([
            'success' => true,
            'message' => 'Country deleted successfully'
        ], 200);
    }


    //get countries by continent
    public function getCountriesByContinent(Request $request, $id)
    {
        $perPage = $request->per_page ?? 10;
        $countries = Country::where('continent_id', $id)->paginate($perPage);

        $countries->transform(function ($country) {
            $imagePath = parse_url($country->image);
            if (isset($imagePath['path'])) {
                $country->image = url($imagePath['path']);
            }
            return [
                'id' => $country->id,
                'continent_id' => $country->continent_id,
                'continent' => $country->continent->name,
                'name' => $country->name,
                'title' => $country->title,
                'image' => $country->image,
                'created_at' => $country->created_at,
                'updated_at' => $country->updated_at
            ];
        });

        if (!$countries) {
            return response()->json([
                'success' => false,
                'message' => 'No countries found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'countries' => $countries
        ], 200);
    }

    /**
     * Destination Methods
     */


    //get all destinations
    public function getDestinations()
    {
        $perPage = request()->per_page ?? 10;
        $destinations = Destination::paginate($perPage);
        if (!$destinations) {
            return response()->json([
                'success' => false,
                'message' => 'No Package found'
            ], 404);
        }
        $destinations->transform(function ($destination) {
            $imagePath = parse_url($destination->image);
            if (isset($imagePath['path'])) {
                $destination->image = url($imagePath['path']);
            }
            return [
                'id' => $destination->id,
                'country_id' => $destination->country_id,
                'country_name' => $destination->country->name,
                'continent_id' => $destination->country->continent->id,
                'continent_name' => $destination->country->continent->name,
                'name' => $destination->name,
                // 'title' => $destination->title,
                'days' => $destination->days,
                'description' => $destination->description,
                'image' => $destination->image,
                'price' => $destination->price,
                'includes_excludes' => json_decode($destination->includes_excludes),
                'hotels' =>json_decode( $destination->hotels),
                'price_validity' => json_decode($destination->price_validity),
                'itinerary' => json_decode($destination->itinerary),
                'created_at' => $destination->created_at,
            ];
        });


        return response()->json([
            'success' => true,
            'destinations' => $destinations
        ], 200);
    }

    //show destination
    public function getDestination($id)
    {
        $destination = Destination::find($id);
        if (!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        }

        $imagePath = parse_url($destination->image);
        if (isset($imagePath['path'])) {
            $destination->image = url($imagePath['path']);
        }

        $destination = [
            'id' => $destination->id,
            'country_id' => $destination->country_id,
            'country_name' => $destination->country->name,
            'continent_id' => $destination->country->continent->id,
            'continent_name' => $destination->country->continent->name,
            'name' => $destination->name,
            // 'title' => $destination->title,
            'days' => $destination->days,
            'description' => $destination->description,
            'image' => $destination->image,
            'price' => $destination->price,
            'includes_excludes' => json_decode($destination->includes_excludes),
            'hotels' => json_decode($destination->hotels),
            'price_validity' => json_decode($destination->price_validity),
            'itinerary' => json_decode($destination->itinerary),
            'created_at' => $destination->created_at,
        ];

        return response()->json([
            'success' => true,
            'destination' => $destination
        ], 200);
    }

    //store destination
    public function storeDestination(Request $request)
    {
       try{
        //check if destination add less than 3
        // $destinationCount = Destination::where('country_id', $request->country_id)->count();
        // if ($destinationCount >= 3) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'You can add maximum 3 destinations for a country'
        //     ], 400);
        // }


        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string',
            'days' => 'required|string',
            'description' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'image' => 'required|image|mimes:jpeg,png,jpg',
            // 'image' => 'nullable',
            'price' => 'required|string',
            'includes_excludes'=>'nullable|json',
            'hotels'=>'nullable|json',
            'price_validity'=>'nullable|json',
            'itinerary'=>'nullable|json',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        //upload image
        $image = $request->file('image');
        if ($request->hasFile('image')) {
            $imageName = time() . rand(100, 999) . '.' . $image->getClientOriginalExtension();
            //check if directory exists
            $uploadPath = public_path('uploads/images/destinations/');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $image->move($uploadPath, $imageName);
            $imageUrl = url('uploads/images/destinations/' . $imageName);
        }



        $destination = new Destination();
        $destination->country_id = $request->country_id;
        $destination->name = $request->name;
        $destination->days = $request->days;
        $destination->description = $request->description;
        $destination->image = $imageUrl;
        // $destination->image = "no image";
        $destination->price = $request->price;
        $destination->includes_excludes = $request->includes_excludes;
        $destination->hotels = $request->hotels;
        $destination->price_validity = $request->price_validity;
        $destination->itinerary = $request->itinerary;
        $destination->save();

        return response()->json([
            'success' => true,
            'message' => 'Package created successfully',
            'destination' => $destination
        ], 201);

       }catch(\Exception $e){
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
       }

    }



    //update destination`
    public function updateDestination(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable|exists:countries,id',
            'name' => 'nullable|string',
            'days' => 'nullable|integer',
            'description' => 'nullable|string',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'price' => 'nullable|numeric',
            'includes_excludes'=>'nullable|json',
            'hotels'=>'nullable|json',
            'price_validity'=>'nullable|json',
            'itinerary'=>'nullable|json',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        $destination = Destination::find($id);
        if (!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        }

        //check image file
        if ($request->hasFile('image')) {
            //delete old image
            $oldImagePath = parse_url($destination->image);

            if (isset($oldImagePath['path']) && file_exists(public_path($oldImagePath['path']))) {
                unlink(public_path($oldImagePath['path']));
            }

            $image = $request->file('image');
            $imageName = time() . rand(100, 999) . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/images/destinations/');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $image->move($uploadPath, $imageName);
            $imageUrl = url('uploads/images/destinations/' . $imageName);
            $imagePath = parse_url($destination->image);
            if (isset($imagePath['path'])) {
                $destination->image = url($imagePath['path']);
            }
            // $destination->image = $imageUrl;
        }
        // $destination->country_id = $request->country_id;
        // $destination->name = $request->name;
        // $destination->days = $request->days;
        // $destination->description = $request->description;
        // $destination->price = $request->price;
        // $destination->includes_excludes = $request->includes_excludes;
        // $destination->hotels = $request->hotels;
        // $destination->price_validity = $request->price_validity;
        // $destination->itinerary = $request->itinerary;

        //check null values
        $request->country_id ? $destination->country_id = $request->country_id : $destination->country_id = $destination->country_id;
        $request->name ? $destination->name = $request->name : $destination->name = $destination->name;
        $request->days ? $destination->days = $request->days : $destination->days = $destination->days;
        $request->description ? $destination->description = $request->description : $destination->description = $destination->description;
        $request->price ? $destination->price = $request->price : $destination->price = $destination->price;
        $request->includes_excludes ? $destination->includes_excludes = $request->includes_excludes : $destination->includes_excludes = $destination->includes_excludes;
        $request->hotels ? $destination->hotels = $request->hotels : $destination->hotels = $destination->hotels;
        $request->price_validity ? $destination->price_validity = $request->price_validity : $destination->price_validity = $destination->price_validity;
        $request->itinerary ? $destination->itinerary = $request->itinerary : $destination->itinerary = $destination->itinerary;
        $destination->save();
        return response()->json([
            'success' => true,
            'message' => 'Package updated successfully',
            'destination' => $destination
        ], 200);

    }

    //delete destination
    public function deleteDestination($id)
    {
        $destination = Destination::find($id);

        if (!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found'
            ], 404);
        }
        //delete image
        $imagePath = parse_url($destination->image);
        if (isset($imagePath['path']) && file_exists(public_path($imagePath['path']))) {
            unlink(public_path($imagePath['path']));
        }
        $destination->delete();
        return response()->json([
            'success' => true,
            'message' => 'Package deleted successfully'
        ], 200);
    }

    //get country wise destinations
    public function getDestinationsByCountry($id)
    {
        $cwDestinations = Country::with('destinations')->find($id);

        if (!$cwDestinations) {
            return response()->json([
                'success' => false,
                'message' => 'No package found'
            ], 404);
        }
        // dd($cwDestinations->image);
        $imagePath = parse_url($cwDestinations->image);
        if (isset($imagePath['path'])) {
            $cwDestinations->image = url($imagePath['path']);
        }

        $cwDestinations->destinations->transform(function ($destination) {
            $imagePath = parse_url($destination->image);
            if (isset($imagePath['path'])) {
                $destination->image = url($imagePath['path']);
            }
            return [
                'id' => $destination->id,
                'country_id' => $destination->country_id,
                'country_name' => $destination->country->name,
                'continent_id' => $destination->country->continent->id,
                'continent_name' => $destination->country->continent->name,
                'package_name' => $destination->name,
                // 'title' => $destination->title,
                'days' => $destination->days,
                'description' => $destination->description,
                'image' => $destination->image,
                'price' => $destination->price,
                'includes_excludes' => json_decode($destination->includes_excludes),
                'hotels' => json_decode($destination->hotels),
                'price_validity' => json_decode($destination->price_validity),
                'itinerary' => json_decode($destination->itinerary),
                'created_at' => $destination->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'country' => $cwDestinations
        ], 200);
    }


    //get getRandomlyDestination
    public function getRandomlyDestination()
    {
        $perPage = request()->per_page ?? 3;
        $destinations = Destination::inRandomOrder()->paginate($perPage);
        if (!$destinations) {
            return response()->json([
                'success' => false,
                'message' => 'No Package found'
            ], 404);
        }
        $destinations->transform(function ($destination) {
            $imagePath = parse_url($destination->image);
            if (isset($imagePath['path'])) {
                $destination->image = url($imagePath['path']);
            }
            return [
                'id' => $destination->id,
                'country_id' => $destination->country_id,
                'country_name' => $destination->country->name,
                'continent_id' => $destination->country->continent->id,
                'continent_name' => $destination->country->continent->name,
                'name' => $destination->name,
                // 'title' => $destination->title,
                'days' => $destination->days,
                'image' => $destination->image,
                'price' => $destination->price,
                'created_at' => $destination->created_at,
            ];
        });


        return response()->json([
            'success' => true,
            'destinations' => $destinations
        ], 200);
    }
}
