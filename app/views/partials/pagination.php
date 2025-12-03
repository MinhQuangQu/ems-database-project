<?php
/**
 * $currentPage : trang hiện tại (int)
 * $totalPages  : tổng số trang (int)
 * $baseUrl     : url cơ sở (string), ví dụ "/employee?page="
 * $queryParams : mảng các query param khác để giữ filter/search ['search' => '...']
 */
function renderPagination(int $currentPage, int $totalPages, string $baseUrl, array $queryParams = [])
{
    if ($totalPages <= 1) return;

    echo '<nav><ul class="pagination">';

    for ($p = 1; $p <= $totalPages; $p++) {
        $queryParams['page'] = $p;
        $url = $baseUrl . '?' . http_build_query($queryParams);
        $activeClass = ($p === $currentPage) ? 'active' : '';
        echo "<li class='page-item {$activeClass}'><a class='page-link' href='{$url}'>{$p}</a></li>";
    }

    echo '</ul></nav>';
}
