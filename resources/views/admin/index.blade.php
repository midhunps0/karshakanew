<x-easyadmin::partials.adminpanel>
    <div x-data="{
        searches: {},
        sorts: {},
        filters: {},
        itemIds: [],
        selectedIds: [],
        pageSelected: false,
        allSelected: false,
        conditions: [{
            field: 'none',
            operations: 'none',
            value: ''
        }],
        indexId: '',
        {{-- advSearchStr: '', --}}
        advSearchStr: $persist('').as(this.indexId),
        paginatorPage: 1,
        itemsCount: 0,
        totalResults: 0,
        downloadUrl: '',
        deleteUrl: '',
        createRoute: '',
        createRouteUrl: '',
        deleteItemId: null,
        showDeleteBox: false,
        advQueryParams() {
            console.log('conditions now:');
            console.log(this.conditions);
            if ((this.conditions.length == 1 && this.conditions[0].field == 'none')) {
                return [];
            }
            let processed = this.conditions.map((c) => {
                return c.field + '::' + c.operation + '::' + c.value;
            });
            console.log(processed);
            return processed;
        },
        doAdvSearch(detail) {
            this.conditions = detail.conditions;
            console.log('detail.condidions in doAdvSearch()');
            console.log(detail.conditions);
            this.advSearchStr = detail.str;
            console.log('this.advSearchStr');
            console.log(this.advSearchStr);
            if ((this.conditions.length == 0 || (this.conditions.length == 1 && this.conditions[0].field == 'none'))) {
                console.log('noconditions = true');
                noconditions = true;
                this.triggerFetch();
            } else {
                console.log('noconditions is false');
                noconditions = false;
            }
            this.runQuery();
        },
        runQuery() {
            this.searches = {};
            this.filters = {};
            if ((this.conditions.length == 0 || (this.conditions.length == 1 && this.conditions[0].field == 'none'))) {
                noconditions = true;
            } else {
                noconditions = false;
            }

            console.log('this.advSearchStr RQ');
            console.log(this.advSearchStr);
            this.triggerFetch();
        },
        processSearches(params) {
            let processed = [];
            let paramkeys = Object.keys(params);
            paramkeys.forEach((k) => {
                processed.push(k + '::' + params[k]);
            });
            return processed;
        },
        processParams(params) {
            let processed = [];
            let paramkeys = Object.keys(params);
            paramkeys.forEach((k) => {
                processed.push(k + '::' + params[k]);
            });
            return processed;
        },
        paramsExceptSelection() {
            let params = {};

            if (Object.keys(this.searches).length > 0) {
                params.search = this.processParams(this.searches);
            }
            if (Object.keys(this.sorts).length > 0) {
                params.sort = this.processParams(this.sorts);
            }
            if (Object.keys(this.filters).length > 0) {
                params.filter = this.processParams(this.filters);
            }
            if (!(this.conditions.length == 1 && this.conditions[0].field == 'none')) {
                x = this.advQueryParams();
                console.log('xxxx');
                console.log(x);
                params.adv_search = [];
                x.forEach((x) => { console.log(x);
                    params.adv_search.push(x);
                });
            }
            console.log('params advsearch');
            console.log(params);
            params.items_count = this.itemsCount;
            params.page = this.paginatorPage;

            return params;
        },
        triggerFetch() {
            let allParams = this.paramsExceptSelection();
            if (this.selectedIds.length > 0) {
                allParams['selected_ids'] = this.selectedIds.join('|');
            }
            console.log('params before fetch');
            console.log(allParams);
            console.log('currentpath');
            console.log(currentpath);
            console.log('request url');
            console.log('{{request()->getRequestUri()}}');
            let ls = '{{request()->getRequestUri()}}'.split('?');
            if (ls.length > 1) {
                let qx = ls[1].split('&');
                let advs = qx.filter((x) => {
                    return x.includes('adv_search');
                });
                if (advs.length > 0) {
                    allParams.adv_search = [advs[0].replace('adv_search[]=', '')];
                }
            }
            $dispatch('linkaction', { link: currentpath.split('?')[0], params: allParams, fresh: true, fragment: 'main-panel', target: 'renderedpanel' });
        },
        doSearch(param) {
            ajaxLoading = true;
            this.paginatorPage = 1;
            this.setParam(param);
            this.triggerFetch();
        },
        setParam(param) {
            let keys = Object.keys(param);
            if (param[keys[0]].length > 0) {
                this.searches[keys[0]] = param[keys[0]];
            } else {
                delete this.searches[keys[0]];
            }
        },
        doSort(detail) {
            this.setSort(detail);
            this.paginatorPage = 1;
            ajaxLoading = true;
            this.triggerFetch();

            $dispatch('clearsorts', { sorts: this.sorts });
        },
        setSort(detail) {
            let keys = Object.keys(detail.data);
            if (detail.exclusive) {
                this.sorts = {};
            }
            if (detail.data[keys[0]] != 'none') {
                this.sorts[keys[0]] = detail.data[keys[0]];
            } else {
                if (typeof(this.sorts[keys[0]]) != 'undefined') {
                    delete this.sorts[keys[0]];
                }
            }
        },
        setFilter(detail) {
            let keys = Object.keys(detail.data);
            if ((detail.data[keys[0]].split('::'))[1] != -1) {
                this.filters[keys[0]] = detail.data[keys[0]];
            } else {
                if (typeof(this.filters[keys[0]]) != 'undefined') {
                    delete this.filters[keys[0]];
                }
            }
        },
        doFilter(detail) {
            this.setFilter(detail);
            ajaxLoading = true;
            this.paginatorPage = 1;
            this.triggerFetch();
        },
        pageUpdateCount(count) {
            this.itemsCount = count;
            this.paginatorPage = 1;
            this.triggerFetch();
        },
        processPageSelect() {
            if (this.pageSelected) {
                this.selectPage();
            } else {
                this.selectedIds = [];
            }
        },
        searchesExceptSelection() {
            let params = {};

            if (Object.keys(this.searches).length > 0) {
                params.search = this.processParams(this.searches);
            }
            if (Object.keys(this.sorts).length > 0) {
                params.sort = this.processParams(this.sorts);
            }
            if (Object.keys(this.filters).length > 0) {
                params.filter = this.processParams(this.filters);
            }
            if (!(this.conditions.length == 1 && this.conditions[0].field == 'none')) {
                params.adv_search = this.advQueryParams();
            }
            params.items_count = this.itemsCount;
            params.page = this.paginatorPage;

            return params;
        },
        selectPage() {
            let lastIndex = this.paginatorPage * this.itemsCount > this.itemIds.length ? this.itemIds.length : this.paginatorPage * this.itemsCount;
            this.selectedIds = this.itemIds.slice((this.paginatorPage - 1) * this.itemsCount, lastIndex);
            this.pageSelected = true;
        },
        selectAll() {
            let params = this.searchesExceptSelection();
            ajaxLoading = true;
            axios.get(selectIdsUrl, { params: params }).then(
                (r) => {
                    this.itemIds = r.data.ids;
                    this.selectedIds = r.data.ids;
                    this.pageSelected = true;
                    this.allSelected = true;
                    ajaxLoading = false;
                }
            ).catch(
                function(e) {
                    console.log(e);
                }
            );
        },
        cancelSelection() {
            this.selectedIds = [];
            this.pageSelected = false;
            this.asllSelected = false;
        },
        setDownloadUrl() {
            let allParams = this.searchesExceptSelection();
            let url_all = getQueryString(allParams);

            if (this.selectedIds.length > 0) {
                allParams.selected_ids = this.selectedIds.join('|');
            }
            let url_selected = getQueryString(allParams);

            $dispatch('downloadurl', { url_all: this.downloadUrl + '?' + url_all, url_selected: this.downloadUrl + '?' + url_selected, idscount: this.selectedIds.length });
        },
        getPaginatedPage(page, link) {
            this.paginatorPage = page;
            {{-- this.triggerFetch(); --}}
            $dispatch('linkaction', { link: link, route: currentroute, fresh: true, fragment: 'main-panel', target: 'renderedpanel' });
        },
        showDeleteWarning(url) {
            this.deleteUrl = url;
            this.showDeleteBox = true;
        },
        doDelete() {
            let fd = new FormData();
            fd.append('_method', 'DELETE');
            $dispatch('formsubmit', { url: this.deleteUrl, formData: fd, target: $el.id });
        },
        cancelDelete() {
            this.deleteItemId = null;
            this.showDeleteBox = false;
        },
        onDeleteResponse(detail) {
            if (detail.target == $el.id) {
                if (detail.content.success) {
                    $dispatch('linkaction', { link: '{{ url()->current() }}', route: '{{ Route::current()->getName() }}' });
                    $dispatch('shownotice', { message: detail.content.message, mode: 'success', redirectUrl: null, redirectRoute: null });
                } else {
                    $dispatch('shownotice', { message: detail.content.message, mode: 'error', redirectUrl: null, redirectRoute: null });
                }
            }
        }
    }"
    x-init="
        if(advSearchStr == undefined) {advSearchStr = '';}
        indexId = '{{ $index_id }}';
        selectIdsUrl = '{{ $selectIdsUrl }}';
        itemIds = JSON.parse('{{ $items_ids }}');
        itemsCount = {{ $items_count }};
        totalResults = {{ $total_results }};
        downloadUrl = '{{ $downloadUrl }}';
        createRoute = '{{ $createRoute }}';
        deleteUrl = '{{ route($destroyRoute, '_0_') }}';
        createRouteUrl = '{{ route($createRoute) }}';
        $watch('selectedIds', (ids) => {
            let pageIds = itemIds.slice((paginatorPage - 1) * itemsCount, paginatorPage * itemsCount);
            pageSelected = pageIds.reduce((result, id) => {
                return result && ids.includes(id);
            }, true);
            allSelected = itemIds.reduce((result, id) => {
                return result && ids.includes(id);
            }, true) && totalResults == itemIds.length;
            setDownloadUrl();
        });
        $nextTick(() => { setDownloadUrl(); });
        if (advSearchStr.length > 0) {
            noconditions = false;
        }
    "
    @countchange.window="pageUpdateCount($event.detail.count);"
    @selectpage="selectPage();"
    @selectall="selectAll();"
    @cancelselection="cancelSelection();"
    @pageselect="processPageSelect();"
    @deleteItem="showDeleteWarning($event.detail.url);"
    @spotsearch.window="doSearch($event.detail)"
    @setparam.window="setParam($event.detail)"
    @spotsort.window="doSort($event.detail)" @setsort.window="setSort($event.detail)"
    @spotfilter.window="doFilter($event.detail);" @setfilter.window="setFilter($event.detail);"
    @pageaction.window="getPaginatedPage($event.detail.page, $event.detail.link);" @advsearch.window="doAdvSearch($event.detail); console.log('its working')"
    @formresponse.window="onDeleteResponse($event.detail);" class="pb-4" id="{{ $index_id }}"
    >
    <h3 class="text-xl font-bold pb-3"><span>{{ $title }}</span>&nbsp;</h3>
    <div class="flex flex-row flex-wrap justify-between items-center space-x-4 mb-2">
        <div class="flex flex-row flex-wrap justify-start items-center space-x-4">
            @if ($showAddButton)
                <a href="#"
                    @click.prevent.stop="$dispatch('linkaction', {link: createRouteUrl, route: createRoute, fresh: true,});"
                    role="button" class="btn btn-sm rounded-md normal-case">Add&nbsp;
                    <x-easyadmin::display.icon icon="easyadmin::icons.plus" />
                </a>
            @endif
            @if (isset($advSearchFields) && count($advSearchFields) > 0)
                <x-easyadmin::utils.advsearchbtn />
            @endif
        </div>
        <div class="flex flex-row flex-wrap justify-end items-center space-x-4">
            <x-easyadmin::utils.panelresize />
            @if ($exportsEnabled)
                <x-easyadmin::utils.export :selectionEnabled="$selectionEnabled"/>
            @endif
        </div>
    </div>
    <div class="my-4">
        <div x-show="advSearchStr.length > 0" class="p-2 border border-base-200 rounded-md m-0">
            <span class="text-warning font-bold">Advanced Search:</span> <span x-text="advSearchStr"></span> <button
                class="btn btn-sm btn-link normal-case text-warning py-0"
                @click.stop.prevent="advSearchStr=''; $dispatch('clearadvsearch');">Clear</button>
        </div>
    </div>
    <div id="indextable" x-data x-init="console.log('working in alpine');"
        @contentupdate.window="console.log('cue captured!');console.log($event.detail.target);console.log($el.id);
                    if ($event.detail.target == $el.id) {
                        $el.innerHTML = $event.detail.content;
                    }
                "
        class="overflow-x-scroll scroll-m-1 relative max-w-full p-0 m-0 mt-2 rounded-md">
        @fragment('indextable')
            @php
                $total_cols = $selectionEnabled ? count($col_headers) + 1 : count($col_headers);
            @endphp
            <table class="table min-w-200 w-full border-2 border-base-200 rounded-md"
                :class="compact ? 'table-mini' : 'table-compact'">

                <thead>
                    <tr>
                        @if ($selectionEnabled)
                            <th class="w-7">
                                <input type="checkbox" x-model="pageSelected" @change="$dispatch('pageselect');"
                                    class="checkbox checkbox-xs"
                                    :class="!allSelected ? 'checkbox-primary' : 'checkbox-secondary'">
                            </th>
                        @endif

                        <x-easyadmin::partials.indexheaders :columns="$col_headers" :searches="$searches" :sorts="$sorts"
                            :filters="$filters" />
                    </tr>
                </thead>
                <tbody>
                    <tr x-show="selectedIds.length > 0" x-transition>
                        <td colspan="{{ $total_cols }}">
                            <div colspan="{{ $total_cols }}"
                                class="text-center bg-warning text-base-200 p-2 rounded-sm">
                                <span x-text="selectedIds.length" class="font-bold"></span>
                                &nbsp;<span class="font-bold">item<span x-show="selectedIds.length > 1">s</span>
                                    selected.</span>
                                &nbsp;<button type="button" @click.prevent.stop="$dispatch('selectpage');"
                                    class="btn btn-xs" :disabled="pageSelected">Select Page</button>
                                &nbsp;<button type="button" @click.prevent.stop="$dispatch('selectall')"
                                    class="btn btn-xs" :disabled="allSelected">Select All
                                    {{ $total_results }} items</button>
                                &nbsp;<button type="button" @click.prevent.stop="$dispatch('cancelselection')"
                                    class="btn btn-xs">Cancel All</button>
                            </div>
                        </td>
                    </tr>


                    @foreach ($results as $result)
                        <tr>
                            @if ($selectionEnabled)
                                <td>
                                    <input type="checkbox" :value="{{ $result->id }}" x-model="selectedIds"
                                        class="checkbox checkbox-primary checkbox-xs">
                                </td>
                            @endif
                            <x-easyadmin::partials.indexfields :result="$result" :columns="$columns" />
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="my-4 p-2">
                {{ $results->appends(\Request::except(['x_ajax', 'x_fr']))->links() }}
            </div>
        @endfragment
    </div>
    @if (isset($advSearchFields) && count($advSearchFields) > 0)
        <x-easyadmin::utils.advsearch :advSearchFields="$advSearchFields" />
    @endif
    <x-dynamic-component component="easyadmin::display.confirm" message="Are you sure you want to delete the item?"
        okFunction="doDelete" cancelFunction="cancelDelete" showKey="showDeleteBox" action_label="Yes" />
</div>
</x-easyadmin::partials.adminpanel>
