import React from 'react';
import PyroSheet from './PyroSheet';
import {BaseLanguageFilter, LanguagesFilter, StreamsFilter} from './Filters';
import _ from 'lodash';

import TranslationService from './../services/TranslationService';

class App extends React.Component {


    constructor(...args) {
        super(...args);

        this.state = {
            initialized: false,
            filterData: {
                streams: [],
                languages: {}
            },
            filters: {
                streams: [],
                baseLanguage: null,
                languages: []
            }
        };
    }

    componentDidMount() {
        // use service to fetch all data via filters
        if(this.state.initialized) {
            return; // already did that
        }
        TranslationService.getFilterData(function(response) {
            var state = {
                filterData: response,
                initialized: true,

                filters: {
                    streams: [],
                    baseLanguage: response.languages.default,
                    languages: []
                }
            };

            // also set defaults
            for(var i = 0; i < response.streams.length; i++) {
                var stream = response.streams[i];
                if(stream.default) {
                    state.filters.streams.push(stream.id);
                }
            }

            var defaultTranslationLocale = _.find(response.languages.supported, function(locale) {
                return locale !== response.languages.default
            });
            state.filters.languages = [defaultTranslationLocale];
            this.setState(state);


        }.bind(this));
    }

    onChanged(type, value) {
        if(!this.state) {
            return; // not initialized yet
        }

        var state = this.state;
        state.filters[type] = value;


        if(this.hasValidFilters(state.filters)) {
            state.valid = true;
            this.updateSheet(state.filters);
        } else {
            state.valid = false;
        }

        this.setState(state);
    }

    hasValidFilters(filters) {
        for(var key in filters) {
            var filter = filters[key];
            if(_.isEmpty(filter)) {
                return false;
            }
        }

        return true;
    }

    updateSheet(filters) {
        this.setState({
            loading: true
        });
        TranslationService.getSheetData(filters, function(response) {
            this.setState({
                loading: false,
                sheetData: response
            });
        }.bind(this));
    }

    render() {
        const filterData = this.state.filterData;

        const streams = filterData.streams;
        const languages = filterData.languages.supported ? filterData.languages.supported : [];

        return (
            <div className="container-fluid">

                <div className="card">
                    <span className="card-label card-label-primary">Filters</span>
                    <div className="card-block row filters">
                        <div className="col-xs-24 col-md-8">
                            <StreamsFilter streams={streams} selected={this.state.filters.streams} onChange={this.onChanged.bind(this, 'streams')} />
                        </div>
                        <div className="col-xs-24 col-md-8">
                            <BaseLanguageFilter languages={languages} selected={this.state.filters.baseLanguage} onChange={this.onChanged.bind(this, 'baseLanguage')} />
                        </div>
                        <div className="col-xs-24 col-md-8">
                            <LanguagesFilter languages={languages} without={this.state.filters.baseLanguage} selected={this.state.filters.languages} onChange={this.onChanged.bind(this, 'languages')} />
                        </div>
                    </div>
                </div>

                <div className="card">
                    <span className="card-label card-label-primary">Translations</span>

                    <div className="card-block row sheet-container">
                        <div className="col-xs-24">
                            {this.state.valid ?
                                this.state.loading ? (
                                    <span>Loading</span>

                                ) : (
                                    <PyroSheet filters={this.state.filters} data={this.state.sheetData} />
                                )
                            :
                                (
                                    <span>
                                        {/* invalid filter state */}
                                        &nbsp;
                                    </span>
                            )}
                        </div>
                    </div>
                </div>


                {/*
                <div className="ro<">
                    <div className="col-xs-6">
                        <h5>Filters</h5>
                        <pre>
                            {JSON.stringify(this.state.filters, null, 2)}
                        </pre>
                    </div>
                    <div className="col-xs-12">
                        <h5>Sheet data</h5>
                        <pre>
                            {JSON.stringify(this.state.sheetData, null, 2)}
                        </pre>
                    </div>
                </div>
                */}

            </div>
        );
    }
}


export default App;
