import React from 'react';
import Sheet from './Sheet';

import TranslationService from './../../services/TranslationService';

class StreamSheet extends React.Component  {

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

    getData(data) {
        //const data = this.props.data ? this.props.data : [];
        let prepared = [];

        for(var i = 0; i < data.length; i++) {
            var stream = data[i];

            for(var j = 0; j < stream.entries.length; j++) {
                var entryContainer = stream.entries[j];
                var entry = entryContainer.entry;

                for(var fieldName in entry) {
                    var row = [];

                    //first push identifier
                    var identifier = stream.identifier + '.' + entryContainer.id + '.' + fieldName;
                    row.push(identifier);

                    var translations = entry[fieldName];
                    for(var locale in translations) {
                        var fieldValue = translations[locale];
                        row.push(fieldValue);
                    }

                    row.push(null); //dummy spacer

                    prepared.push(row);
                }
            }
        }

        return prepared;
    }

    handleSave(event) {
        //1. get all changes
        const changes = this.state.changes;

        this.setState({
            saving: true
        });


        TranslationService.saveStreamTranslations(changes, function(response) {
            this.onSaved(response);

            this.setState({
                saving: false
            });
        }.bind(this));
    }

    onSaved(response) {
        if(response && response.fail && response.fail.length === 0) {
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

        console.info('render', {sheet});

        return (
            <div>

                {sheet && sheet.data && sheet.data.length ? (
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

export default StreamSheet;
