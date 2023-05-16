var config = {
    map: {
        '*': {
            'location': 'Perspective_Geolocation/js/location'
        }
    },
    paths: {
        'location' : "Perspective_Geolocation/js/location",
    },
    shim: {

        'location': {
            deps: ['jquery']
        }
    }
};

