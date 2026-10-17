<?php
namespace GOF\Helpers;

class Paginator {
    private int $total;
    private int $perPage;
    private int $currentPage;
    private string $baseUrl;

    public function __construct(int $total, int $perPage, int $currentPage, string $baseUrl = '') {
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = max(1, $currentPage);
        $this->baseUrl = $baseUrl;
    }

    public function offset(): int {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function totalPages(): int {
        return (int)ceil($this->total / max(1, $this->perPage));
    }

    public function hasPrev(): bool {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool {
        return $this->currentPage < $this->totalPages();
    }

    public function getCurrentPage(): int {
        return $this->currentPage;
    }

    public function getTotal(): int {
        return $this->total;
    }

    public function getPerPage(): int {
        return $this->perPage;
    }

    public function links(): string {
        $totalPages = $this->totalPages();
        if ($totalPages <= 1) return '';

        $html = '<nav aria-label="Page navigation"><ul class="pagination">';
        
        $qs = $_GET;
        unset($qs['page']);
        $queryString = empty($qs) ? '' : '&' . http_build_query($qs);
        
        $url = function($page) use ($queryString) {
            $base = $this->baseUrl ?: parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            return $base . '?page=' . $page . $queryString;
        };

        if ($this->hasPrev()) {
            $html .= '<li class="page-item"><a class="page-link" href="'.$url($this->currentPage - 1).'">Previous</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }

        $start = max(1, $this->currentPage - 3);
        $end = min($totalPages, $this->currentPage + 3);
        
        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="'.$url(1).'">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i === $this->currentPage) {
                $html .= '<li class="page-item active"><span class="page-link">'.$i.'</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="'.$url($i).'">'.$i.'</a></li>';
            }
        }

        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link" href="'.$url($totalPages).'">'.$totalPages.'</a></li>';
        }

        if ($this->hasNext()) {
            $html .= '<li class="page-item"><a class="page-link" href="'.$url($this->currentPage + 1).'">Next</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }
}
