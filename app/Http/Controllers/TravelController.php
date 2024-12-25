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
    public function getCountries()
    {
        $countries = Country::all();
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

    //store country
    public function storeCountry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'continent_id' => 'required|exists:continents,id',
            'name' => 'required|string|unique:countries',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }
        $country = new Country();
        $country->continent_id = $request->continent_id;
        $country->name = $request->name;
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
            'name' => 'required|string|unique:countries',
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
        $country->continent_id = $request->continent_id;
        $country->name = $request->name;
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
        $country->delete();
        return response()->json([
            'success' => true,
            'message' => 'Country deleted successfully'
        ], 200);
    }


    //get countries by continent
    public function getCountriesByContinent($id)
    {
        $countries = Country::where('continent_id', $id)->get();
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
        $destinations = Destination::all();
        if (!$destinations) {
            return response()->json([
                'success' => false,
                'message' => 'No destinations found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'destinations' => $destinations
        ], 200);
    }

    //store destination
    public function storeDestination(Request $request)
    {
       try{
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'name' => 'nullable|string',
            'title' => 'nullable|string',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'price' => 'required|numeric',
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
            $imageName = time() . rand(100, 999) . '_' . $image->getClientOriginalExtension();
            //check if directory exists
            if (!file_exists(public_path('uploads/images'))) {
                mkdir(public_path('uploads/images'), 0777, true);
            }
            $image->move(public_path('uploads/images'), $imageName);
        }
        $imageUrl = url('uploads/images/' . $imageName);


        $destination = new Destination();
        $destination->country_id = $request->country_id;
        $destination->name = $request->name;
        $destination->title = $request->title;
        $destination->description = $request->description;
        $destination->image = $imageUrl;
        $destination->price = $request->price;
        $destination->save();

        return response()->json([
            'success' => true,
            'message' => 'Destination created successfully',
            'destination' => $destination
        ], 201);

       }catch(\Exception $e){
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
       }

    }
}
