<form method="GET">

    <div class="input-group">

        <input
            type="text"
            name="keyword"
            class="form-control"
            placeholder="<?= $placeholder ?? 'Search...' ?>"
            value="<?= html_escape($keyword ?? '') ?>">

        <button
            class="btn btn-primary">

            <i class="fas fa-search"></i>
            Search

        </button>

    </div>

</form>