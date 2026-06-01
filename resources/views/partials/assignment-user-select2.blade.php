<script>
    window.initAssignmentUserSelect2 = window.initAssignmentUserSelect2 || function ($root) {
        var $scope = $root && $root.length ? $root : $(document);

        function assignmentUserMatcher(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            if (typeof data.text === 'undefined') {
                return null;
            }

            var term = params.term.toLowerCase();
            var search = (data.element && $(data.element).data('search'))
                ? String($(data.element).data('search')).toLowerCase()
                : String(data.text || '').toLowerCase();

            return search.indexOf(term) > -1 ? data : null;
        }

        $scope.find('select.assignment-user-select').each(function () {
            var $el = $(this);

            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }

            var ph = $el.data('placeholder');
            var opts = {
                width: '100%',
                allowClear: false,
                minimumResultsForSearch: 0,
                matcher: assignmentUserMatcher,
            };

            if (ph) {
                opts.placeholder = ph;
                opts.allowClear = true;
            }

            $el.select2(opts);
        });
    };
</script>
