/**
 * useFormatters Composable
 * 
 * This composable provides common formatting utilities for the application.
 * Use this for consistent formatting of dates, currency, and other data types.
 */
import { CalendarDate, getLocalTimeZone } from '@internationalized/date'

export function useFormatters() {
    /**
     * Format date from YYYY-MM-DD to DD-MM-YYYY
     * @param dateString - Date string in YYYY-MM-DD format
     * @returns Formatted date string in DD-MM-YYYY format or '-' if empty
     */
    const formatDate = (dateString: string): string => {
        if (!dateString) return '-'

        // Remove time part if exists
        const cleanDate = dateString.split('T')[0]
        const [year, month, day] = cleanDate.split('-')
        if (!year || !month || !day) return '-'

        return `${day}-${month}-${year}`
    }

    /**
     * Format number as Indonesian Rupiah currency
     * @param value - Numeric value to format
     * @returns Formatted currency string or '-' if null/undefined
     */
    const formatCurrency = (value: number): string => {
        if (value === null || value === undefined) return '-'
        if (value === 0) return '0'
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value)
    }

    /**
     * Convert CalendarDate to string format for API (YYYY-MM-DD)
     * @param calendarDate - CalendarDate object
     * @returns Date string in YYYY-MM-DD format or null if undefined
     */
    const getDateString = (calendarDate: CalendarDate | undefined): string | null => {
        if (!calendarDate) return null
        const date = calendarDate.toDate(getLocalTimeZone())
        const year = date.getFullYear()
        const month = String(date.getMonth() + 1).padStart(2, '0')
        const day = String(date.getDate()).padStart(2, '0')
        return `${year}-${month}-${day}` // Returns YYYY-MM-DD format
    }

    /**
     * Convert YYYY-MM-DD date string to CalendarDate object
     * @param dateString - Date string in YYYY-MM-DD format
     * @returns CalendarDate object or undefined if invalid
     */
    const stringToCalendarDate = (dateString: string): CalendarDate | undefined => {
        if (!dateString) return undefined
        const [year, month, day] = dateString.split('-')
        return new CalendarDate(Number(year), Number(month), Number(day))
    }

    return {
        formatDate,
        formatCurrency,
        getDateString,
        stringToCalendarDate
    }
}
