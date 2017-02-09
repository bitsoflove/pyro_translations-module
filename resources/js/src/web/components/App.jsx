import React from "react";
import PyroSheet from "./PyroSheet";
import {BaseLanguageFilter, LanguagesFilter, StreamsFilter, ModulesFilter} from "./Filters";
import _ from "lodash";
import TranslationService from "./../services/TranslationService";

class App extends React.Component {


    constructor(...args) {
        super(...args);

        this.state = {
            initialized: false,

            filterData: {
                streams: [],
                languages: {}
            },
            translationType: 'streams',
            filters: {
                baseLanguage: null,
                languages: [],
                streams: [],
                modules: []
            }
        };
    }

    componentDidMount() {
        if (! this.state.initialized) {
            this.getFilterData();
        }
    }


    // could have been done using cerebral (signal -> action)
    getFilterData() {
        TranslationService.getFilterData(function (response) {
            var state = {
                filterData: response,
                initialized: true,

                filters: {
                    streams: [],
                    modules: [],
                    baseLanguage: response.languages.default,
                    languages: []
                }
            };

            // also set defaults
            for (var i = 0; i < response.streams.length; i++) {
                var stream = response.streams[i];
                if (stream.default) {
                    state.filters.streams.push(stream.id);
                }
            }

            // also set defaults
            for (var j = 0; j < response.modules.length; j++) {
                var module = response.modules[j];
                if (module.default) {
                    state.filters.modules.push(module.id);
                }
            }

            var defaultTranslationLocale = _.find(response.languages.supported, function (locale) {
                return locale !== response.languages.default
            });
            state.filters.languages = [defaultTranslationLocale];
            this.setState(state);
        }.bind(this));
    }

    onChanged(type, value) {
        if (!this.state) {
            return; // not initialized yet
        }

        var state = this.state;
        var filters = state.filters;
        filters[type] = value;


        if (this.hasValidFilters(filters)) {
            state.valid = true;
            this.updateSheet(filters, this.state.translationType);
        } else {
            state.valid = false;
        }

        state.filters = filters;
        this.setState(state);
    }

    hasValidFilters(filters) {
        for (var key in filters) {
            var filter = filters[key];
            if (_.isEmpty(filter)) {
                return false;
            }
        }

        return true;
    }

    updateSheet(filters, translationType) {

        const sanitizedFilters = this.sanitizeFilters(filters, translationType);
        this.setState({
            loading: true
        });
        TranslationService.getSheetData(sanitizedFilters, translationType, function (response) {
            this.setState({
                loading: false,
                sheetData: response
            });
        }.bind(this));
    }

    sanitizeFilters(filters, translationType) {
        var sanitized = JSON.parse(JSON.stringify(filters));

        switch(translationType) {
            case 'modules':
                delete sanitized.streams;
                break;
            case 'streams':
                delete sanitized.modules;
                break;
        }

        return sanitized;
    }

    setTranslationType(type) {
        this.setState({
            translationType: type
        });
    }

    render() {
        const filterData = this.state.filterData;

        const streams = filterData.streams;
        const modules = filterData.modules;
        const languages = filterData.languages.supported ? filterData.languages.supported : [];
        const state = this.state;

        const translationType = state.translationType;

        return (
            <div className="container-fluid">

                <div className="card">
                    <span className="card-label card-label-primary">Filters</span>
                    <span className="pull-right">

                        {/* I know, I know. use components... */}
                        <div className="btn-group" data-toggle="buttons">
                            <label onClick={this.setTranslationType.bind(this, 'streams')} className={"btn btn-info btn-sm " + ((state.translationType === 'streams') ? 'active' : '')}>
                                <input type="checkbox" defaultChecked={translationType === 'streams'} autoComplete="off" />
                                Streams
                            </label>
                            <label onClick={this.setTranslationType.bind(this, 'modules')} className={"btn btn-info btn-sm " + ((state.translationType === 'modules') ? 'active' : '')}>
                                <input type="checkbox" defaultChecked={translationType === 'modules'} autoComplete="off" />
                                Modules
                            </label>
                        </div>
                    </span>

                    <div className="card-block row filters">
                        <div className="col-xs-24 col-md-8">
                            {translationType === 'streams' ? (
                                <StreamsFilter
                                    streams={streams}
                                    selected={this.state.filters.streams}
                                    onChange={this.onChanged.bind(this, 'streams')}/>
                            ) : undefined}

                            {translationType === 'modules' ? (
                                <ModulesFilter
                                    modules={modules}
                                    selected={this.state.filters.modules}
                                    onChange={this.onChanged.bind(this, 'modules')}/>
                            ) : undefined}

                        </div>
                        <div className="col-xs-24 col-md-8">
                            <BaseLanguageFilter
                                languages={languages}
                                selected={this.state.filters.baseLanguage}
                                onChange={this.onChanged.bind(this, 'baseLanguage')}/>
                        </div>
                        <div className="col-xs-24 col-md-8">
                            <LanguagesFilter
                                languages={languages}
                                without={this.state.filters.baseLanguage}
                                selected={this.state.filters.languages}
                                onChange={this.onChanged.bind(this, 'languages')}/>
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
                                    <PyroSheet filters={this.state.filters} data={this.state.sheetData}/>
                                 )
                            : (
                                <span>
                                    {/* invalid filter state */}
                                    &nbsp;
                                </span>
                             )}
                        </div>
                    </div>
                </div>


                <div className="row">
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
                {/**/}

            </div>
    );
    }
    }


    export default App;
