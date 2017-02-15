import React from 'react';
import Sheet from './Sheet';
import HotTable from 'react-handsontable';

import TranslationService from './../../services/TranslationService';

import classnames from 'classnames';

class ModuleSheet extends React.Component  {

    constructor(props) {
        super(props);

        this.state = {
            changes: {},
            sheet: {
                initialized: true,
                data: this.getData(props.data),
                colHeaders: this.getColHeaders()
            }
        };

        this.handleChange = this.handleChange.bind(this);
    }
    setSheet(data) {
        var sheet =  {
            initialized: true,
            data: this.getData(data),
            colHeaders: this.getColHeaders()
        };
        this.setState({sheet: sheet});
    }
    getSheet() {
        return this.state.sheet;
    }

    getColHeaders() {
        var header = ['key', this.props.filters.baseLanguage].concat(this.props.filters.languages);
        header.push('Actions'); //actions spacer
        return header;
    }

    afterChange(changes, source) {
        if(!changes) {
            return;
        }

        var state = this.state;

        for(var i = 0; i < changes.length; i++) {
            var change = changes[i];
            this.handleChange(state, change);
        }

        this.setState(state);
    }

    handleChange(state, change) {
        const row = change[0];
        const col = change[1];
        const oldValue = change[2];
        const newValue = change[3];

        var sheet = this.getSheet();
        var identifier = this.getIdentifier(sheet, row, col);


        var data = {
            row: row,
            column: col,

            oldValue: oldValue,
            newValue: newValue
        };

        state.changes[identifier] = data;

        console.info('changes updated', data);
    }


    /**
     * Identifier shows:
     *  stream, field, language (and row + col)
     */

    getIdentifier(sheet, row, col) {
        var rowData = sheet.data[row];
        var identifier = rowData[0];

        var language = this.props.filters.languages[col-2];
        return identifier + '.' + language;
    }


    buildIdentifier(moduleNamespace, translationKey) {
        return '' + translationKey;
    }

    getData(data) {

        let prepared = [];

        for(var moduleNamespace in data) {
            var moduleTranslations = data[moduleNamespace];

            // for now, safely assume all translations are ordered the way the should be
            for(var translationKey in moduleTranslations) {

                var rowIdentifier = this.buildIdentifier(moduleNamespace, translationKey);

                var row = [
                    rowIdentifier
                ];

                var localeTranslations = moduleTranslations[translationKey];

                for(var locale in localeTranslations) {
                    var translation = localeTranslations[locale];
                    row.push(translation);
                }

                row.push(null); // dummy spacer
                prepared.push(row);
            }
        }

        return prepared;
    }

    handleSave(event) {

        debugger;

        //1. get all changes
        const changes = this.state.changes;

        this.setState({
            saving: true
        });

        TranslationService.saveModuleTranslations(changes, function(response) {
            this.onSaved(response);

            this.setState({
                saving: false
            });
        }.bind(this));
    }

    onSaved(response) {
        debugger;
        if(response && (!response.fail || response.fail.length === 0)) {
            alert('saved');
        }
    }

    componentWillReceiveProps(nextProps) {
        // You don't have to do this check first, but it can help prevent an unneeded render
        var data = nextProps && nextProps.data ? nextProps.data : [];
        this.setSheet(data);
    }

    shouldComponentUpdate(nextProps, nextState) {
        var currentData = this.props.data;
        var nextData = nextProps.data;

        var sameData = (JSON.stringify(currentData) === JSON.stringify(nextData));

        return !sameData;
    }

    render() {
        var sheet = this.getSheet();

        return (
            <div>

                {sheet ? (
                    <Sheet data={sheet.data}
                           colHeaders={sheet.colHeaders}
                           afterChange={this.afterChange.bind(this)}
                    />
                ) : (
                    <span>No sheet data</span>
                )}

                <div className="pull-right">
                    <button className="btn btn-primary" onClick={this.handleSave.bind(this)}>Save</button>
                </div>
            </div>
        );
    }
}

export default ModuleSheet;
