

<head>
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet" >
</head>
<div class="sidebar">
    <ul class="tree">
        <li>
            <details>
                <summary class="tree_parent">
                    Invetory</summary>
                <ul>
                    <li>
                            <ul >
                                {{-- Sorting --}}
                                {{-- <a class="sort" href="{{ route ('SortByKind') }}">Sort by Kind</a> --}}
                                
                                <a href="{{ route('gold-items.create') }}">Gold Inventory</a>
                                <a href="{{ route('gold-items.create') }}">Diamond Inventory</a>
                                <a href="{{ route('gold-pounds.index') }}">Coins</a>
                                <a href="{{ route('gold-items.create') }}">Bars</a>
                                <a href="{{ route('gold-items.create') }}">Chains</a>
                                <a href="{{ route('gold-items.index') }}">All Items</a>
                                {{-- <a class="sort" href="{{ route('SortByNameAsc', array_merge(request()->query(), ['sort' => 'name', 'order' => 'asc'])) }}" class="btn btn-primary">Sort by A-Z</a>
                                <a class="sort" href="{{ route('SortByNameDes', array_merge(request()->query(), ['sort' => 'name', 'order' => 'desc'])) }}" class="btn btn-primary">Sort by Z-A</a>
                                <a class="sort" href="{{ route('SortByNew', array_merge(request()->query(), ['sort' => 'updated_at', 'order' => 'desc'])) }}" class="btn btn-primary">Sort by new</a>
                                <a class="sort" href="{{ route('SortByOld', array_merge(request()->query(), ['sort' => 'updated_at', 'order' => 'asc'])) }}" class="btn btn-primary">Sort by old</a> --}}
                            </ul>
                    </li>
                </ul>
            </details>
        </li>
    </ul>
    {{-- <ul class="tree">
        <li>
            <details>
                <summary class="tree_parent">
                    Catalogs</summary>
                <ul>
                    <li>
                            <ul >
                                <a class="sort" href="{{ route ('gold_catalog.3') }}">GoldCatalog</a>
                                <a class="sort" href="{{ route ('diamond_catalog.3') }}" class="btn btn-primary">DiamondCatalog</a>
                                <a class="sort" href="{{ route ('SortByNameDes') }}" class="btn btn-primary">......</a>
                           </ul>
                    </li>
                </ul>
            </details>
        </li>
    </ul> --}}
    {{-- <form action="{{ route('fetch.catalog.data') }}" method="POST">
        @csrf
        <ul class="tree">
            <li>
                <details>
                    <summary class="tree_parent">Kind</summary>
                    <ul>
                        <li>
                            <ul>
                                <li>
                                    <input type="checkbox" class="catalog" id="ring" name="catalogs[]" value="Ring">
                                    <label for="ring">Ring</label>
                                </li>
                                <li>
                                    <input type="checkbox" class="catalog" id="pendant" name="catalogs[]" value="Pendant">
                                    <label for="pendant">Pendant</label>
                                </li>
                                <li>
                                    <input type="checkbox" class="catalog" id="necklace" name="catalogs[]" value="Necklace">
                                    <label for="necklace">Necklace</label>
                                </li>
                                <li>
                                    <input type="checkbox" class="catalog" id="anklet" name="catalogs[]" value="Anklet">
                                    <label for="anklet">Anklet</label>
                                </li>
                                <li>
                                    <input type="checkbox" class="catalog" id="bracelet" name="catalogs[]" value="Bracelet">
                                    <label for="bracelet">Bracelet</label>
                                </li>
                                <li>
                                    <input type="checkbox" class="catalog" id="brooch" name="catalogs[]" value="Brooch">
                                    <label for="brooch">Brooch</label>
                                </li>
                                <li>
                                    <input type="checkbox" class="catalog" id="cufflink" name="catalogs[]" value="Cufflink">
                                    <label for="cufflink">Cufflink</label>
                                </li>
                                <li>
                                    <input type="checkbox" class="catalog" id="earring" name="catalogs[]" value="Earring">
                                    <label for="earring">Earring</label>
                                </li>
                                <li>
                                    <input type="checkbox" class="catalog" id="medal" name="catalogs[]" value="Medal">
                                    <label for="medal">Medal</label>
                                </li>
                                <li>
                                    <input type="checkbox" class="catalog" id="watch" name="catalogs[]" value="Watch">
                                    <label for="watch">Watch</label>
                                </li>
                            </ul>
                        </li>
                    </ul>
        <button type="submit" class="btn btn-primary">Apply</button>
                </details>
            </li>
            
        </ul>
    </form> --}}

</div>
