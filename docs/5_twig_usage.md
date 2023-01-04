# Twig usage

## Basic usage

After passing a collection to twig we can iterate over it as usual in Doctrine Collections:

```twig
<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
    {% for user in collection %}
        <tr>
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
        </tr>
    {% endfor %}
    </tbody>
</table>
```

## Pagination info

You can show pagination info:

```twig
Current page: {{ collections.page }}
Total pages: {{ collections.pages }}
Total results: {{ collections.total }}
Results per page: {{ collections.rpp }}
```

## Pagination links

And you can show pagination links:

```twig
<tfoot>
    <tr>
        <td colspan="3">
            <nav>
                <ul class="pagination justify-content-center flex-wrap mb-0">
                    {% if not collection.isFirstPage %}
                        <li>
                            <a href="{{ collection.firstPageUrl(app.request) }}">
                                Prev page
                            </a>
                        </li>
                    {% endif %}
            
                    {% for page in collection.collapsedPages(7, true) %}
                        {% if page == null %}
                            <li>...</li>
                        {% else %}
                            <li class="{{ page == collection.page | default(false)? ' active' : ''}}">
                                <a href="{{ collection.pageUrl(app.request, page) }}">
                                    {{ page }}
                                </a>
                            </li>
                        {% endif %}
                    {% endfor %}
            
                    {% if not collection.isLastPage %}
                        <li>
                            <a href="{{ collection.lastPageUrl(app.request) }}">
                                Next page
                            </a>
                        </li>
                    {% endif %}
                </ul>
            </nav>

        </td>
    </tr>
</tfoot>
```

## Sorting results

```twig
<th>
    <a href="{{ accounts.sortToggleUrl(app.request, 'id') }}">
        Id
        {% if accounts.sortedBy('id', 'asc') %}<i class="up-icon"></i>{% elseif accounts.sortedBy('id', 'desc') %}<i class="down-icon"></i>{% endif %}
    </a>
</th>
<th>
    <a href="{{ accounts.sortToggleUrl(app.request, 'name') }}">
        Name
        {% if accounts.sortedBy('name', 'asc') %}<i class="up-icon"></i>{% elseif accounts.sortedBy('name', 'desc') %}<i class="down-icon"></i>{% endif %}
    </a>
</th>
<th>
    <a href="{{ accounts.sortToggleUrl(app.request, 'email') }}">
        Email
        {% if accounts.sortedBy('email', 'asc') %}<i class="up-icon"></i>{% elseif accounts.sortedBy('email', 'desc') %}<i class="down-icon"></i>{% endif %}
    </a>
</th>
```

