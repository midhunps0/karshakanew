import axios from 'axios';

export default () => ({
    page: null,
    ajax: false,
    showPage: true,
    ajaxLoading: false,
    panelId: 'renderedpanel',
    async initAction() {
        let link = window.landingUrl;
        let route = window.landingRoute;
        let el = document.getElementById(this.panelId);
        while (el == null) {
            await window.sleep(50);
            el = document.getElementById(this.panelId);
        }
        this.$store.app.xpages[link] = el.innerHTML;
        // history.pushState({href: link}, '', link);
        history.pushState({href: link, route: route, target: this.panelId, fragment: 'main-panel'}, '', link);
    },
    historyAction(e) {
        if (e.state != undefined && e.state != null) {
            let link = e.state.href;
            let route = e.state.route;
            let target = e.state.target;
            let fragment = e.state.fragment;
            this.showPage = false;
            this.ajaxLoading = true;
            if (this.$store.app.xpages[link] != undefined) {
                setTimeout(() => {
                    this.showPage = true;
                    // this.page = this.$store.app.xpages[link];
                    this.$dispatch('pagechanged', {currentpath: link, currentroute: route, target: target, fragment: fragment});
                    this.$dispatch('contentupdate', {content: this.$store.app.xpages[link], target: target});

                    this.ajaxLoading = false;
                },
                    100
                );
            } else {
                setTimeout(() => {
                        this.showPage = true;
                        this.ajaxLoading = false;
                    },
                    100
                );
            }
        }
    },
    getQueryString(params) {
        let thelink = "";
            let keys = Object.keys(params);

            for (let j=0; j < keys.length; j++) {
                if (Array.isArray(params[keys[j]])) {
                    for (let x = 0; x < params[keys[j]].length; x++) {
                        thelink += keys[j]+'[]=' + params[keys[j]][x];
                        if (x < (params[keys[j]].length -1)) {
                            thelink += '&';
                        }
                    }
                } else {
                    thelink += keys[j]+'=' + params[keys[j]];
                }

                if (j < (keys.length - 1)) {
                    thelink += '&';
                }
            }
            return thelink;
    },
    fetchLink(detail) {
        let targetPanelId;
        let theRoute = detail.route;
        let fr = (typeof detail.fragment != 'undefined' && detail.fragment != null) ? detail.fragment : 'main-panel';
        let forceFresh = typeof detail.fresh != 'undefined' && detail.fresh === true;
        let trackHistory = typeof detail.history == 'undefined' ? true : detail.history;
        if (typeof detail.target == 'undefined' || detail.target == null) {
            targetPanelId = this.panelId;
        } else {
            targetPanelId = detail.target;
        }
        let link = detail.link;
        let params = detail.params;
        console.log('params');
        console.log(params);
        let thelink = link;
        if (detail.params != null) {
            if (typeof detail.params.items_count != 'undefined') {
                let r1 = thelink.match(/items_count=\w+/);
                thelink = thelink.replace(r1, "");
            }
            if (typeof detail.params.page != 'undefined') {
                let r2 = thelink.match(/page=\w+/);
                thelink = thelink.replace(r2, "");
            }
            thelink += "?" + this.getQueryString(params);
        }
        if (!forceFresh && this.$store.app.xpages != undefined && this.$store.app.xpages[thelink] != undefined) {
            this.showPage = false;
            this.ajaxLoading = true;
            if (this.$store.app.xpages[thelink] != undefined) {
                setTimeout(() => {
                    this.showPage = true;
                    this.$dispatch('contentupdate', {content: this.$store.app.xpages[thelink], target: targetPanelId});
                    this.$dispatch('pagechanged', {currentpath: link, currentroute: detail.route});
                    this.ajaxLoading = false;
                },
                    100
                );
            } else {
                setTimeout(() => {
                    this.showPage = true;
                    this.ajaxLoading = false;
                },
                    100
                );
            }
            if (trackHistory) {
                history.pushState({href: thelink, route: theRoute, target: targetPanelId, fragment: fr}, '', thelink);
            }
        } else {
            console.log('fetching data..');
            this.$store.app.pageloading = true;
            // if (params != null) {
            //     params['x_mode'] = 'ajax';
            //     params['x_fr'] = fr;
            // } else {
            //     params = {x_mode: 'ajax'};
            //     params = {x_fr: fr};
            // }
            this.ajaxLoading = true;
            axios.get(
                link,
                {
                    params: params,
                    headers: {
                        'X-FR': fr
                    }
                }
              ).then(
                (r) => {
                    console.log('data fetched! panel: '+targetPanelId);
                    this.showPage = false;
                    this.ajax = true;
                    setTimeout(
                        () => {
                            // document.getElementById(targetPanelId).innerHTML = r.data;
                            this.$dispatch('contentupdate', {content: r.data, target: targetPanelId});
                            // this.$dispatch('pagechanged', {currentpath: link, currentroute: detail.route});
                            // this.page = r.data;
                            this.showPage = true;
                            this.ajaxLoading = false;
                        },
                        100
                    );
                    if (this.$store.app.xpages == undefined || this.$store.app.xpages == null) {
                        this.$store.app.xpages = [];
                    }
                    this.$store.app.xpages[thelink] = r.data;
                    if (trackHistory) {
                        history.pushState({href: thelink, route: theRoute, target: targetPanelId, fragment: fr}, '', thelink);
                    }
                    this.$dispatch('pagechanged', {currentpath: link, currentroute: detail.route});
                    this.$store.app.pageloading = false;
                }
            ).catch(
                function (e) {
                    console.log(e);
                }
            );
        }
    },
    resetPages() {
        this.$store.app.xpages = [];
    },
    postForm(data) {
        this.ajaxLoading = true;
        axios.post(data.url, data.formData, {
            headers: {
            'Content-Type': 'multipart/form-data',
            'X-FR': data.fragment
            }
        }).then((r) => {
            this.ajaxLoading = false;
            this.$dispatch('formresponse', {target: data.target, content: r.data});
        }).catch((e) => {
            this.ajaxLoading = false;
            console.log('error:');
            this.$dispatch('formresponse', {target: data.target, content: e});
        });
    }
});
