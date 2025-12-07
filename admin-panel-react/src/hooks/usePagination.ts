import { useState, useCallback } from 'react';
import { PaginationParams } from '../types';

interface UsePaginationReturn {
  paginationParams: PaginationParams;
  setPage: (page: number) => void;
  setPageSize: (pageSize: number) => void;
  setSearch: (search: string) => void;
  setSorting: (sortBy: string, sortDescending: boolean) => void;
  resetPagination: () => void;
}

const DEFAULT_PAGE = 1;
const DEFAULT_PAGE_SIZE = 20;

export function usePagination(
  initialPageSize: number = DEFAULT_PAGE_SIZE
): UsePaginationReturn {
  const [paginationParams, setPaginationParams] = useState<PaginationParams>({
    page: DEFAULT_PAGE,
    pageSize: initialPageSize,
    sortBy: undefined,
    sortDescending: false,
    search: undefined,
  });

  const setPage = useCallback((page: number) => {
    setPaginationParams((prev) => ({ ...prev, page }));
  }, []);

  const setPageSize = useCallback((pageSize: number) => {
    setPaginationParams((prev) => ({ ...prev, pageSize, page: DEFAULT_PAGE }));
  }, []);

  const setSearch = useCallback((search: string) => {
    setPaginationParams((prev) => ({
      ...prev,
      search: search || undefined,
      page: DEFAULT_PAGE,
    }));
  }, []);

  const setSorting = useCallback((sortBy: string, sortDescending: boolean) => {
    setPaginationParams((prev) => ({ ...prev, sortBy, sortDescending }));
  }, []);

  const resetPagination = useCallback(() => {
    setPaginationParams({
      page: DEFAULT_PAGE,
      pageSize: initialPageSize,
      sortBy: undefined,
      sortDescending: false,
      search: undefined,
    });
  }, [initialPageSize]);

  return {
    paginationParams,
    setPage,
    setPageSize,
    setSearch,
    setSorting,
    resetPagination,
  };
}

export default usePagination;
