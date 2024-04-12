<div id="exportModal" class="modal">
    <div class="modal-content">
        <h2>Export data</h2>
        <form id="exportForm" action="{{ route('books.export') }}" method="post">
            @csrf
            @method('GET')
            <fieldset>
                <legend>Include</legend>
                <label for="exportOption1" class="radio-btn">
                    <input type="radio" id="exportOption1" name="exportOption" value="books" checked/>
                    Titles and authors
                </label>
                <label for="exportOption2" class="radio-btn">
                    <input type="radio" id="exportOption2" name="exportOption" value="titles"/>
                    Titles only
                </label>
                <label for="exportOption3" class="radio-btn">
                    <input type="radio" id="exportOption3" name="exportOption" value="authors"/>
                    Authors only
                </label>
            </fieldset>

            <fieldset>
                <legend>Save as</legend>
                <label for="exportAs1" class="radio-btn">
                    <input type="radio" id="exportAs1" name="exportAs" value="csv" checked>
                    .csv
                </label>
                <label for="exportAs2" class="radio-btn">
                    <input type="radio" id="exportAs2" name="exportAs" value="xml"/>
                    .xml
                </label>
            </fieldset>

            <!-- Table filter and sort data -->
            <input type="hidden" name="search" value="{{ $searchString }}">
            <input type="hidden" name="sortBy" value="{{ $sortBy }}">
            <input type="hidden" name="sortOrder" value="{{ $sortOrder }}">

            <div class="form-btns">
                <button id="exportCancelBtn" type="button">Cancel</button>
                <button id="exportSubmitBtn" type="submit">Export</button>
            </div>
        </form>
    </div>
</div>
