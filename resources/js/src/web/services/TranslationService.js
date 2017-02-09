import axios from 'axios';

class TranslationService  {
    getFilterData(callback) {
        var endpoint = '/admin/translations/api/filters';
        console.info(endpoint);
        axios.get(endpoint)
            .then(function(response) {
                callback(response.data);
            })
            .catch(function(error) {
                this.handleError(error);
            }.bind(this));
    };


    getSheetData(filters, type, callback) {
        var endpoint = '/admin/translations/api/sheet';
        var qs = [
            'type=' + type,
            'base-language=' + filters.baseLanguage,
            'locales=' + filters.languages.join(',')
        ];

        if(filters.streams) {
            qs.push('streams=' + filters.streams.join(','));
        }
        if(filters.modules) {
            qs.push('modules=' + filters.modules.join(','));
        }

        endpoint += ('?' + qs.join('&'));
        console.info(endpoint);
        axios.get(endpoint, filters).then(function(response) {
            callback(response.data);
        }).catch(function (error) {
            this.handleError(error);
        }.bind(this));
    }

    save(changes, callback) {
        var request = {};

        //2. build proper request that's easy to parse by the backend
        for(var identifier in changes) {
            var change = changes[identifier];

            var identifierSplit = identifier.split('.');
            var module = identifierSplit[0];
            var stream = identifierSplit[1];
            var entityId = identifierSplit[2];
            var field = identifierSplit[3];
            var locale = identifierSplit[4];
            var value = change.newValue;

            request[identifier] = {
                module,
                stream,
                entityId,
                field,
                locale,
                value
            };
        }

        var endpoint = '/admin/translations/api/save';

        // for some reason, POST not working properly with axios on my machine
        // jQuery will do the trick
        jQuery.post(endpoint, request, function(response) {
            callback(response);
        }).error(function(error) {
            debugger;
        });

    }

    handleError(error) {
        if (error.response) {
            // The request was made, but the server responded with a status code
            // that falls out of the range of 2xx
            console.log(error.response.data);
            console.log(error.response.status);
            console.log(error.response.headers);
        } else {
            // Something happened in setting up the request that triggered an Error
            console.log('Error', error.message);
        }

        console.log(error.config);
    }
}


export default new TranslationService;
