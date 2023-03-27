
export default () => (
    {
        //        values: [],
        keyname: null,
        oldVals: [],
        select_options: [],
        search: '',
        errors: '',
        multiple: false,
        search: '',
        options: [],
        optionsType: 'collection',
        optionsIdKey: null,
        optionsTextkey: null,
        optionsDisplayKeys: null,
        selected: [],
        selectedVals: [],
        show: false,
        selId: '',
        fireInputEvent: false,
        resetSources: [],
        toggleListeners: {},
        showelement: true,
        fetchOptions(val) {
            axios.get(
                this.fetchUrl,
                {
                    params: { 'value': val }
                }
            ).then((r) => {
                this.select_options = [];
                console.log(r.data.results);
                let ops = r.data.results;
                if (OptionsType == 'key_value') {
                    Object.keys(ops).forEach((key) => {
                        this.select_options.push({ key: key, text: ops[key] });
                    });
                } else if(OptionsType == 'value_only') {
                    ops.forEach((op) => {
                        this.select_options.push({ key: op, text: op });
                    });
                } else if(this.optionsType == 'collection') {
                    if (this.optionsDisplayKeys != null) {
                        ops.forEach((op) => {
                            this.select_options.push({ key: op[optionsIdKey], text: op[optionsIdKey] });
                        });
                    } else {
                        ops.forEach((op) => {
                            let obj = {
                                key: op[this.optionsIdKey],
                                text: op[this.optionsTextKey],
                            };
                            this.optionDisplayKeys.foreach((k) => {
                                if (k != this.optionsTextKey) {
                                    obj[k] = op[k];
                                }
                            });
                        });
                    }
                }
                this.initOptions();
            }).catch((e) => {

            });
        },
        open() { this.show = true; this.focusOnList(); },
        close() { this.show = false; this.search = ''; },
        isOpen() { return this.show === true },
        select(val, event) {
            let theoption = this.options.filter((op) => {
                return op.value == val;
            })[0];
            if (!theoption.selected) {
                if (!this.multiple) {
                    this.selected = [];
                    this.selectedVals = [];
                }
                this.options.forEach((op) => {
                    if (op.value == val) {
                        op.selected = true;
                    } else if (!this.multiple) {
                        op.selected = false;
                    }
                });

                this.selected.push(theoption);
                this.selectedVals.push(val);
            } else {
                this.options.forEach((op) => {
                    if (op.value == val) {
                        op.selected = false;
                    }
                });
                this.selected = this.selected.filter((item) => {
                    return item.value != val;
                });
                this.selectedVals = this.selectedVals.filter((item) => {
                    return item != val;
                });
            }

            if (this.fireInputEvent) {
                $dispatch('eaforminputevent', { source: this.name, value: JSON.parse(JSON.stringify(this.selectedVals)), multiple: this.multiple });
            }

            this.focusOnList();
            this.search = '';
            if (!this.multiple) {
                this.close();
            }
        },
        remove(index, val) {
            let theoption = this.selected.filter((op) => {
                return op.value == val;
            })[0];
            this.options.forEach((op) => {
                if (op.value == val) {
                    op.selected = false;
                }
            });
            /* this.options = this.options.filter((op) => {
                return op.value != val;
            }); */
            this.selected = this.selected.filter((op) => {
                return op.value != val;
            });
            this.selectedVals = this.selectedVals.filter((v) => {
                return v != val;
            });

            if (this.fireInputEvent) {
                $dispatch('eaforminputevent', { source: this.keyname, value: JSON.parse(JSON.stringify(this.selectedVals)), multiple: this.multiple });
            }
        },
        initOptions() {
            let intvalues = this.selectedVals.map((v) => {
                return parseInt(v);
            });
            this.options = [];
            for (let i = 0; i < this.select_options.length; i++) {
                let op = {
                    value: this.select_options[i].key,
                    text: this.select_options[i].text,
                };
                Object.keys(this.select_options[i]).forEach((k) => {
                    if (k != 'key' && k != 'text') {
                        op[k] = this.select_options[i][k];
                    }
                });
                this.options.push(op);
                /* this.options.push({
                    value: this.select_options[i].key,
                    text: this.select_options[i].text,
                    selected: intvalues.includes(parseInt(this.select_options[i].key))
                }); */
            }
            console.log('inside Init    options: 3');
            if (this.selectedVals.length > 0) {
                for (i = 0; i < this.options.length; i++) {
                    if (intvalues.includes(parseInt(this.options[i].value))) {
                        this.selected.push(this.options[i]);
                    }
                };
            }
            console.log('options:');
            console.log(this.options);
        },
        focusOnList() {
            $nextTick(() => {
                let item = document.getElementById('slinput');
                setTimeout(() => {
                    if (item != null && item != undefined) {
                        item.focus();
                    }
                }, 100);

            });
        },
        resetOnEvent(detail) {
            console.log('event captured'); console.log(this.resetSources); console.log(detail);
            if (this.resetSources.includes(detail.source)) {
                console.log('reset initiated..');
                this.reset();
                if (!detail.multiple) {
                    this.fetchOptions(detail.value[0]);
                }
            }
        },
        reset() {
            this.selected = [];
            this.selectedVals = [];
        },
        toggleOnEvent(source, value) {
            if (Object.keys(this.toggleListeners).includes(source)) {
                this.toggleListeners[source].forEach((item) => {
                    switch (item.condition) {
                        case '==':
                            if (item.value == value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '!=':
                            if (item.value != value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '>':
                            if (item.value > value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '<':
                            if (item.value < value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '>=':
                            if (item.value >= value) {
                                this.showelement = item.show;
                            }
                            break;
                        case '<=':
                            if (item.value <= value) {
                                this.showelement = item.show;
                            }
                            break;
                    }
                });
            }
        }
    }
);
