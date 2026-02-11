@extends('layouts.masterDopm')

@section('title', 'DOPM & IKK - Dashboard')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Filter Dropdown Styles */
    .btn-filter {
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        color: #374151;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-filter:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
        color: #111827;
    }
    
    .btn-filter:focus {
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }
    
    .btn-filter.dropdown-toggle::after {
        margin-left: 0.5rem;
    }
    
    .dropdown-menu {
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 8px;
        padding: 0.5rem 0;
        margin-top: 0.5rem;
    }
    
    .dropdown-item.filter-option {
        padding: 0.5rem 1rem;
        transition: all 0.15s ease;
    }
    
    .dropdown-item.filter-option:hover {
        background-color: #f3f4f6;
        color: #111827;
    }
    
    .dropdown-item.filter-option:active {
        background-color: #e5e7eb;
    }
    
    /* Layer Toggle Button Styles */
    .layer-toggle-btn {
        position: relative;
    }
    
    .layer-toggle-btn.active {
        background-color: #3b82f6;
        color: #ffffff;
        border-color: #3b82f6;
    }
    
    .layer-toggle-btn.active:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    
    .layer-toggle-btn:not(.active) {
        background-color: #ffffff;
        color: #6b7280;
        opacity: 0.7;
    }
    
    .layer-toggle-btn:not(.active):hover {
        background-color: #f9fafb;
        opacity: 1;
    }
    
    /* Map Sidebar Panel Styles */
    .map-sidebar {
        position: absolute;
        top: 0;
        right: 0;
        width: 380px;
        height: 100%;
        background: #ffffff;
        box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: flex;
        transition: transform 0.3s ease;
        transform: translateX(0);
    }
    
    .map-sidebar.collapsed {
        transform: translateX(calc(100% - 50px));
    }
    
    .sidebar-toggle-btn {
        position: absolute;
        left: -40px;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 60px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-right: none;
        border-radius: 8px 0 0 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 1001;
        box-shadow: -2px 0 4px rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-toggle-btn:hover {
        background: #f9fafb;
        box-shadow: -2px 0 6px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-toggle-btn i {
        font-size: 24px;
        color: #6b7280;
        transition: transform 0.3s ease;
    }
    
    .map-sidebar.collapsed .sidebar-toggle-btn i {
        transform: rotate(180deg);
    }
    
    .sidebar-content {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .sidebar-tabs {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #e5e7eb;
        padding: 8px;
        gap: 4px;
        overflow-x: auto;
        overflow-y: hidden;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8f9fa;
    }
    
    .sidebar-tabs::-webkit-scrollbar {
        height: 6px;
    }
    
    .sidebar-tabs::-webkit-scrollbar-track {
        background: #f8f9fa;
    }
    
    .sidebar-tabs::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .sidebar-tabs::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .sidebar-tab {
        flex: 0 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px 16px;
        background: transparent;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        min-height: 70px;
        min-width: 80px;
        max-width: 120px;
    }
    
    .sidebar-tab:hover {
        background: #e9ecef;
    }
    
    .sidebar-tab.active {
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-tab i {
        font-size: 24px;
        color: #6b7280;
        margin-bottom: 4px;
    }
    
    .sidebar-tab.active i {
        color: #3b82f6;
    }
    
    .sidebar-tab .tab-label {
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 4px;
    }
    
    .sidebar-tab.active .tab-label {
        color: #111827;
        font-weight: 600;
    }
    
    .sidebar-tab .tab-count {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        background: #e5e7eb;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 24px;
        text-align: center;
    }
    
    .sidebar-tab.active .tab-count {
        background: #3b82f6;
        color: #ffffff;
    }
    
    .sidebar-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .sidebar-search {
        display: flex;
        align-items: center;
        padding: 12px;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        gap: 8px;
    }
    
    .sidebar-search .search-icon {
        color: #9ca3af;
        font-size: 20px;
    }
    
    .sidebar-search-input {
        flex: 1;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s ease;
    }
    
    .sidebar-search-input:focus {
        border-color: #3b82f6;
    }
    
    .sidebar-filter-btn {
        background: transparent;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .sidebar-filter-btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }
    
    .sidebar-filter-btn i {
        font-size: 20px;
        color: #6b7280;
    }
    
    .sidebar-list-container {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }
    
    .tab-content {
        display: none;
        height: 100%;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .sidebar-list {
        padding: 8px;
    }
    
    .sidebar-list-item {
        display: flex;
        align-items: center;
        padding: 12px;
        margin-bottom: 8px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .sidebar-list-item:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-list-item.active {
        background: #eff6ff;
        border-color: #3b82f6;
    }
    
    .list-item-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
        color: #ffffff;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .list-item-content {
        flex: 1;
        min-width: 0;
    }
    
    .list-item-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
        word-break: break-word;
    }
    
    .list-item-subtitle {
        font-size: 12px;
        color: #6b7280;
        word-break: break-word;
    }
    
    .list-item-time {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }
    
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #9ca3af;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    
    .empty-state p {
        font-size: 14px;
        margin: 0;
    }
    
    /* Map Selection Styles for Evaluasi */
    .map-selection-container {
        padding: 16px;
    }
    
    .map-selection-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .map-selection-title i {
        font-size: 20px;
        color: #3b82f6;
    }
    
    .map-selection-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .map-selection-item {
        position: relative;
        cursor: pointer;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
        border: 2px solid #e5e7eb;
        background: #ffffff;
    }
    
    .map-selection-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }
    
    .map-selection-item.selected {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        background: #eff6ff;
    }
    
    .map-selection-item.selected::after {
        content: '';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: #3b82f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
    
    .map-selection-item.selected::before {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        color: #ffffff;
        font-size: 14px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 11;
    }
    
    .map-thumbnail {
        width: 100%;
        height: 100px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .map-thumbnail.map-1 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .map-thumbnail.map-2 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .map-thumbnail.map-3 {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .map-thumbnail.map-4 {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    
    .map-thumbnail.map-5 {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }
    
    .map-thumbnail.map-6 {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }
    
    .map-thumbnail-pattern {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0.3;
        background-image: 
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px),
            repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px);
    }
    
    .map-thumbnail-icon {
        position: relative;
        z-index: 1;
        font-size: 32px;
        color: rgba(255, 255, 255, 0.9);
    }
    
    .map-selection-label {
        padding: 10px 12px;
        text-align: center;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
    }
    
    .map-selection-item.selected .map-selection-label {
        color: #3b82f6;
        font-weight: 600;
        background: #eff6ff;
    }
    
    .map-selection-description {
        font-size: 11px;
        color: #6b7280;
        margin-top: 4px;
        line-height: 1.4;
    }
    
    .map-selection-info {
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-top: 16px;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.6;
    }
    
    .map-selection-info i {
        font-size: 16px;
        color: #3b82f6;
        margin-right: 6px;
        vertical-align: middle;
    }
    
    /* Control Room specific styles */
    .sidebar-list-item[data-type="controlroom"] {
        flex-direction: column;
        align-items: stretch;
        padding: 0;
        overflow: hidden;
        position: relative;
        border-radius: 8px;
        margin-bottom: 8px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    
    .sidebar-list-item[data-type="controlroom"]:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-list-item[data-type="controlroom"].expanded {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }
    
    .sidebar-list-item[data-type="controlroom"] .sidebar-list-item-header {
        padding: 14px 16px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: flex;
        align-items: center;
    }
    
    .sidebar-list-item[data-type="controlroom"] .sidebar-list-item-header:hover {
        background-color: #f9fafb;
    }
    
    .sidebar-list-item[data-type="controlroom"].expanded .sidebar-list-item-header {
        background-color: #f0f9ff;
        border-bottom: 1px solid #e0f2fe;
    }
    
    .sidebar-list-item[data-type="controlroom"]:hover .list-item-expand-icon {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
    
    .sidebar-list-item[data-type="controlroom"].expanded .list-item-expand-icon {
        transform: rotate(180deg);
        background-color: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }
    
    .controlroom-cctv-list {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s ease;
        background-color: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }
    
    .sidebar-list-item[data-type="controlroom"].expanded .controlroom-cctv-list {
        max-height: 600px;
        padding: 8px 0;
        overflow-y: auto;
    }
    
    .controlroom-cctv-item {
        padding: 10px 16px 10px 48px;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        background-color: transparent;
    }
    
    .controlroom-cctv-item:last-child {
        border-bottom: none;
    }
    
    .controlroom-cctv-item:hover {
        background-color: #ffffff;
        padding-left: 52px;
    }
    
    .controlroom-cctv-item.active {
        background-color: #e0f2fe;
        border-left: 3px solid #3b82f6;
        padding-left: 49px;
    }
    
    .controlroom-cctv-item::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #d1d5db;
        transition: all 0.2s ease;
    }
    
    .controlroom-cctv-item:hover::before {
        background-color: #3b82f6;
        width: 8px;
        height: 8px;
    }
    
    .controlroom-cctv-item.active::before {
        background-color: #3b82f6;
        width: 8px;
        height: 8px;
    }
    
    .list-item-expand-icon {
        margin-left: auto;
        color: #6b7280;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease, color 0.2s ease;
        flex-shrink: 0;
        font-size: 20px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }
    
    /* Collapsed state styles */
    .map-sidebar.collapsed {
        width: 50px;
    }
    
    .map-sidebar.collapsed .sidebar-content {
        width: 50px;
    }
    
    .map-sidebar.collapsed .sidebar-tabs {
        flex-direction: column;
        padding: 8px 4px;
        gap: 4px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    
    .map-sidebar.collapsed .sidebar-tab {
        min-height: 50px;
        padding: 8px 4px;
        width: 100%;
        min-width: auto;
        max-width: none;
    }
    
    .map-sidebar.collapsed .sidebar-tab .tab-label,
    .map-sidebar.collapsed .sidebar-tab .tab-count {
        display: none;
    }
    
    .map-sidebar.collapsed .sidebar-tab i {
        margin-bottom: 0;
    }
    
    .map-sidebar.collapsed .sidebar-body {
        display: none;
    }
    
    .map-sidebar.collapsed .sidebar-toggle-btn {
        left: -40px;
    }
    
    .hazard-detection-header {
        margin-bottom: 24px;
    }

    .hazard-detection-title {
        font-size: 24px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 8px;
    }

    .hazard-detection-subtitle {
        font-size: 14px;
        color: #6b7280;
    }

    /* Detail Total CCTV modal styles */
    #totalCctvModal .modal-content {
        background-color: #F8FAFC;
    }

    #totalCctvModal .modal-body {
        background-color: #F8FAFC;
    }

    #totalCctvModal .card {
        background-color: #ffffff;
        border: none;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    #totalCctvModal .card .card-body {
        background-color: #ffffff;
    }

    /* Custom column for 5 items */
    @media (min-width: 992px) {
        .col-lg-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
    }

    .stats-card {
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .stats-card.total {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stats-card.active {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .stats-card.resolved {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .stats-card.critical {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }

    .stats-number {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stats-label {
        font-size: 14px;
        opacity: 0.9;
    }

    /* Animation untuk angka yang berubah */
    #modalJumlahAreaKritis,
    #modalCctvAreaKritis,
    #modalCctvAreaNonKritis {
        transition: transform 0.3s ease, color 0.3s ease;
    }

    #modalJumlahAreaKritis.animating,
    #modalCctvAreaKritis.animating,
    #modalCctvAreaNonKritis.animating {
        transform: scale(1.1);
        color: #3b82f6;
    }

    /* Animation untuk badge persentase */
    #detailPersentaseCctvAreaKritis,
    #detailPersentaseCctvAreaNonKritis {
        transition: transform 0.2s ease;
    }

    /* Popup style untuk OpenLayers - diperlukan untuk map */
    .ol-popup {
        position: absolute;
        background-color: white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        bottom: 12px;
        left: -50px;
        min-width: 200px;
    }

    .ol-popup:after, .ol-popup:before {
        top: 100%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }

    .ol-popup:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
    }

    .ol-popup:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
    }

    .ol-popup-closer {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
        color: #333;
        font-size: 18px;
        font-weight: bold;
    }

    .ol-popup-closer:hover {
        color: #000;
    }
    
    /* Map container - menggunakan class dari template */
    #hazardMap {
        width: 100%;
        height: 600px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        background-color: #f9fafb;
    }
    
    /* Map container responsive */
    @media (max-width: 1200px) {
        #hazardMap {
            height: 500px;
        }
    }
    
    @media (max-width: 768px) {
        #hazardMap {
            height: 400px;
        }
    }
    
    /* Hazard item interaction - menggunakan class Bootstrap */
    .hazard-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .hazard-item:hover {
        background-color: #f9fafb;
    }
    
    .hazard-item.selected {
        background-color: #eff6ff;
        border-left: 3px solid #3b82f6;
        padding-left: 12px;
    }

    .hazard-card {
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        padding: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        position: relative;
        overflow: hidden;
    }

    .hazard-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 18px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(14,165,233,.4), rgba(59,130,246,.2));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    .hazard-card-critical::after {
        background: linear-gradient(135deg, rgba(239,68,68,.5), rgba(249,115,22,.4));
    }

    .hazard-card-high::after {
        background: linear-gradient(135deg, rgba(249,115,22,.45), rgba(251,191,36,.35));
    }

    .hazard-card-medium::after {
        background: linear-gradient(135deg, rgba(59,130,246,.4), rgba(14,165,233,.3));
    }

    .hazard-card-low::after {
        background: linear-gradient(135deg, rgba(34,197,94,.45), rgba(16,185,129,.35));
    }

    .hazard-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .hazard-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .hazard-pill-critical {
        background: rgba(239,68,68,.12);
        color: #b91c1c;
    }

    .hazard-pill-high {
        background: rgba(251,191,36,.18);
        color: #b45309;
    }

    .hazard-pill-medium {
        background: rgba(59,130,246,.15);
        color: #1d4ed8;
    }

    .hazard-pill-low {
        background: rgba(16,185,129,.16);
        color: #047857;
    }

    .hazard-card-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        margin-top: 12px;
    }

    .hazard-card-meta span {
        display: block;
        font-size: 12px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .hazard-card-meta strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
        margin-top: 4px;
    }

    .hazard-card-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 18px;
    }

    .hazard-chip {
        background: rgba(15,23,42,.04);
        color: #0f172a;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
    }

    .hazard-card-actions .btn {
        border-radius: 999px;
        padding: 6px 16px;
    }

    /* Area Kritis Card Styles */
    .area-kritis-card {
        transition: all 0.2s ease;
    }

    .area-kritis-card:hover {
        border-color: #d1d5db !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .area-icon-wrapper {
        transition: background-color 0.2s ease;
    }

    .area-kritis-card-1:hover .area-icon-wrapper {
        background: #fee2e2 !important;
    }

    .area-kritis-card-2:hover .area-icon-wrapper {
        background: #ffedd5 !important;
    }

    .area-kritis-card-3:hover .area-icon-wrapper {
        background: #dcfce7 !important;
    }

    .luasan-info-card {
        border-radius: 14px;
        border: 1px solid rgba(59, 130, 246, 0.15);
        background: rgba(219, 234, 254, 0.6);
        padding: 14px;
    }
    
    /* CCTV Icon Style */
    .cctv-icon-marker {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        border: 3px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .cctv-icon-marker:hover {
        transform: rotate(-45deg) scale(1.1);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.5);
    }
    
    .cctv-icon-marker::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: #ffffff;
        border-radius: 50%;
        transform: rotate(45deg);
    }
    
    .cctv-icon-marker::after {
        content: '📹';
        position: absolute;
        transform: rotate(45deg);
        font-size: 16px;
        z-index: 1;
    }

    /* Notification Styles */
    .notification-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 400px;
    }

    .notification-item {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        animation: slideInRight 0.3s ease-out;
        border-left: 4px solid #ef4444;
        position: relative;
        overflow: hidden;
    }

    .notification-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #ef4444, #f97316);
        animation: progressBar 5s linear forwards;
    }

    .notification-item.success {
        border-left-color: #10b981;
    }

    .notification-item.success::before {
        background: linear-gradient(90deg, #10b981, #34d399);
    }

    .notification-item.warning {
        border-left-color: #f59e0b;
    }

    .notification-item.warning::before {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }

    .notification-item.info {
        border-left-color: #3b82f6;
    }

    .notification-item.info::before {
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
    }

    .notification-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .notification-item.success .notification-icon {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .notification-item.warning .notification-icon {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .notification-item.info .notification-icon {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 600;
        font-size: 14px;
        color: #111827;
        margin-bottom: 4px;
    }

    .notification-message {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }

    .notification-close {
        position: absolute;
        top: 8px;
        right: 8px;
        background: transparent;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .notification-close:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #111827;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes progressBar {
        from {
            width: 100%;
        }
        to {
            width: 0%;
        }
    }

    .notification-item.hiding {
        animation: slideOutRight 0.3s ease-out forwards;
    }
    /* Modal Detail & Intervensi: tampil full dengan Bootstrap standar */
    #detailDopmModal .modal-dialog,
    #intervensiDopmModal .modal-dialog {
        max-width: 90%;
        margin: 0.5rem auto;
    }
    #detailDopmModal .modal-content,
    #intervensiDopmModal .modal-content {
        min-height: 85vh;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    #detailDopmModal .modal-body,
    #intervensiDopmModal .modal-body {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 400px;
    }
    #detailDopmModal .modal-content { background: #fff; }
    #detailDopmModal .modal-header { background: #fff; color: #111827; border-bottom: 1px solid #e5e7eb; }
    #detailDopmModal .modal-body { background: #fff; }
    #detailDopmModal .modal-stat-cards { background: #fff; border-bottom: 1px solid #e5e7eb; }
    #detailDopmModal .stat-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; }
    #detailDopmModal #ipkIkkTableWrap.d-none,
    #detailDopmModal #okkTableWrap.d-none,
    #detailDopmModal #oakTableWrap.d-none { display: none !important; }
    #detailDopmModal #ipkIkkTableWrap:not(.d-none),
    #detailDopmModal #okkTableWrap:not(.d-none),
    #detailDopmModal #oakTableWrap:not(.d-none) { display: block !important; }
    #detailDopmModal #ipkIkkLoading.d-none,
    #detailDopmModal #okkLoading.d-none,
    #detailDopmModal #oakLoading.d-none { display: none !important; }
    #detailDopmModal #ipkIkkEmpty:not(.d-none),
    #detailDopmModal #okkEmpty:not(.d-none),
    #detailDopmModal #oakEmpty:not(.d-none) { display: block !important; }
    #detailDopmModal #ipkIkkEmpty.d-none,
    #detailDopmModal #okkEmpty.d-none,
    #detailDopmModal #oakEmpty.d-none { display: none !important; }
    #detailDopmModal #tableIpkIkk,
    #detailDopmModal #tableOkk,
    #detailDopmModal #tableOak { width: 100%; background: #fff; }
    #intervensiDopmModal .intervensi-section { display: block !important; min-height: 120px; }
    #intervensiDopmModal .intervensi-section .card { margin-bottom: 1rem; }
    #intervensiDopmModal #intervensiIpkLoading:not(.d-none),
    #intervensiDopmModal #intervensiOkkLoading:not(.d-none),
    #intervensiDopmModal #intervensiOakLoading:not(.d-none) { display: block !important; }
    #intervensiDopmModal #intervensiIpkEmpty:not(.d-none),
    #intervensiDopmModal #intervensiOkkEmpty:not(.d-none),
    #intervensiDopmModal #intervensiOakEmpty:not(.d-none) { display: block !important; }
    #intervensiDopmModal #intervensiIpkTableWrap:not(.d-none),
    #intervensiDopmModal #intervensiOkkTableWrap:not(.d-none),
    #intervensiDopmModal #intervensiOakTableWrap:not(.d-none) { display: block !important; }
    .dopm-matriks-row.hover-border:hover { border-color: rgba(0,0,0,0.1) !important; background: rgba(0,0,0,0.02); }
    .dopm-matriks-row.cursor-pointer { cursor: pointer; }
    #tableDopmHarian thead th { white-space: nowrap; }
    #tableIkkClickhouseHarian thead th { white-space: nowrap; }

    /* Summary harian per site */
    .dopm-summary-card { border-radius: 1rem; overflow: hidden; border: 1px solid #e5e7eb; }
    .dopm-summary-card .card-header { font-weight: 600; padding: 0.875rem 1rem; border-bottom: 1px solid #e5e7eb; }
    .dopm-summary-table { font-size: 0.8125rem; }
    .dopm-summary-table thead th { white-space: nowrap; font-weight: 600; padding: 0.5rem 0.75rem; }
    .dopm-summary-table tbody td { padding: 0.5rem 0.75rem; vertical-align: middle; }
    .dopm-summary-table tbody tr:hover { background-color: #f8fafc; }
    .dopm-summary-total { font-weight: 700; background-color: #f1f5f9 !important; }
    .dopm-summary-badge-hijau { background-color: #dcfce7; color: #166534; font-weight: 600; }
    .dopm-summary-badge-kuning { background-color: #fef9c3; color: #854d0e; font-weight: 600; }
    .dopm-summary-badge-merah { background-color: #fee2e2; color: #991b1b; font-weight: 600; }
    .dopm-summary-jenis-col { max-width: 10rem; overflow: hidden; text-overflow: ellipsis; }

    /* Scroll area kartu matriks: tinggi tetap, scrollbar disembunyikan */
    .dopm-matriks-list-scroll {
        max-height: 320px;
        overflow-y: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .dopm-matriks-list-scroll::-webkit-scrollbar {
        display: none;
    }
    
    .cctv-icon-marker.live::before {
        background: #10b981;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    /* Animation for Total CCTV Count */
    #totalCctvCountDynamic {
        transition: all 0.3s ease;
    }
    
    #totalCctvCountDynamic.updating {
        opacity: 0.6;
        transform: scale(0.95);
    }
    
    #totalCctvCountDynamic.pulse-animation {
        animation: pulseCctv 0.6s ease-in-out;
    }
    
    @keyframes pulseCctv {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
            color: #10b981;
        }
        100% {
            transform: scale(1);
        }
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@8.2.0/ol.css">
<link rel="stylesheet" href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
<div class="hazard-detection-header">
    <h1 class="hazard-detection-title">DOPM & IKK - Dashboard</h1>
    <p class="hazard-detection-subtitle">Statistik harian DOPM, IPK-IKK, OKK, dan OAK (Observasi Area Kerja)</p>

    {{-- Filter tanggal --}}
    <!-- <div class="card rounded-4 mb-3 w-100">
        <div class="card-body py-3">
            <form method="get" action="{{ route('dopmikk.dopm.dashboard') }}" class="row g-10 align-items-end w-100" id="dashboardFilterForm">
                <div class="col-auto">
                    <label for="filterDate" class="form-label mb-0 small fw-semibold">Tampilkan data tanggal</label>
                    <input type="date" name="date" id="filterDate" class="form-control" value="{{ $filterDate ?? now()->toDateString() }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary rounded-3" id="dashboardFilterBtn">
                        <i class="material-icons-outlined me-1" style="font-size: 18px;">search</i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div> -->

    <div class="card rounded-4 mb-3 w-100">
    <div class="card-body py-3">
        <form method="get" action="{{ route('dopmikk.dopm.dashboard') }}" id="dashboardFilterForm">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md">
                    <label for="filterDate" class="form-label mb-2 small fw-semibold text-muted">
                        <i class="material-icons-outlined me-1" style="font-size: 16px; vertical-align: middle;">calendar_today</i>
                        Tampilkan data tanggal
                    </label>
                    <input type="date" 
                           name="date" 
                           id="filterDate" 
                           class="form-control rounded-3" 
                           value="{{ $filterDate ?? now()->toDateString() }}">
                </div>
                <div class="col-12 col-md">
                    <label for="filterSite" class="form-label mb-2 small fw-semibold text-muted">
                        <i class="material-icons-outlined me-1" style="font-size: 16px; vertical-align: middle;">place</i>
                        Site
                    </label>
                    <select name="site" id="filterSite" class="form-select rounded-3">
                        <option value="" {{ ($filterSite ?? '') === '' ? 'selected' : '' }}>Semua Site</option>
                        @foreach($siteList ?? [] as $site)
                            <option value="{{ $site }}" {{ ($filterSite ?? '') === $site ? 'selected' : '' }}>{{ $site }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <button type="submit" class="btn btn-primary rounded-3 px-4" id="dashboardFilterBtn">
                        <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">search</i> 
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
    
</div>

    {{-- <div class="mb-3">
        <button class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardStatsCollapse" aria-expanded="true" aria-controls="dashboardStatsCollapse">
            <span class="fw-bold d-flex align-items-center">
                <i class="material-icons-outlined me-2">dashboard</i>
                Statistik DOPM, IKK, OKK & OAK
            </span>
            <i class="material-icons-outlined collapse-icon">expand_less</i>
        </button>
    </div> --}}
    
        <div class="row">
          <div class="col-12 col-xl-4 d-flex">
             <div class="card rounded-4 w-100">
               <div class="card-body">
                 <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="">
                      <h2 class="mb-0">{{ number_format($totalDopmMingguIni ?? 0) }}</h2>
                    </div>
                    <div class="">
                      <p class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-danger text-danger bg-opacity-10"><span class="material-icons-outlined fs-6">arrow_downward</span>8.6%</p>
                    </div>
                  </div>
                  <p class="mb-0">Total Week ini</p>
                   <div id="chart1"></div>
               </div>
             </div>
          </div>
          <div class="col-12 col-xl-8 d-flex">
            <div class="card rounded-4 w-100">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-around flex-wrap gap-4 p-4">
                  <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                    <a href="javascript:;" class="mb-2 wh-48 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                      <i class="material-icons-outlined">assignment</i>
                    </a>
                    <h3 class="mb-0">{{ number_format($totalWorkPermitApprovedHarian ?? 0) }}</h3>
                    <p class="mb-0">DOPM</p>
                     <small class="text-muted">Data Hari ini</small>
                  </div>
                  <div class="vr"></div>
                  <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                    <a href="javascript:;" class="mb-2 wh-48 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                      <i class="material-icons-outlined">checklist</i>
                    </a>
                    <h3 class="mb-0">{{ $pctIkkAdaIpk ?? 0 }}%</h3>
                                <p class="mb-0">IKK ada IPK</p>
                                <small class="text-muted">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaIpkCount ?? 0) }} belum IPK-IKK</small>
                  </div>
                  <div class="vr"></div>
                  <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                    <a href="javascript:;" class="mb-2 wh-48 bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center">
                      <i class="material-icons-outlined">folder_open</i>
                    </a>
                    <h3 class="mb-0">{{ $pctIkkAdaOkk ?? 0 }}%</h3>
                                <p class="mb-0">IKK ada OKK</p>
                                <small class="text-muted">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaOkkCount ?? 0) }} belum OKK</small>
                  </div>
                  <div class="vr"></div>
                  
                  <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                    <a href="javascript:;" class="mb-2 wh-48 bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center">
                      <i class="material-icons-outlined">visibility</i>
                    </a>
                   <h3 class="mb-0">{{ number_format($totalOakHarian ?? 0) }}</h3>
                                <p class="mb-0">OAK</p>
                                <small class="text-muted">Data Hari ini (OBSERVE, Layer 2/3/4)</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div><!--end row-->
        
        <div class="row">
          <div class="col-12 col-xl-5 col-xxl-4 d-flex">
            <div class="card rounded-4 w-100 shadow-none bg-transparent border-0">
               <div class="card-body p-0">
                 <div class="row g-4">
                    <div class="col-12 col-xl-6 d-flex">
                      <div class="card mb-0 rounded-4 w-100">
                       <div class="card-body">
                         <div class=" mb-2">
                           <h5 class="mb-0 fw-bold">Dopm Cancel</h5>
                           <p class="mb-0 text-muted small">Total DOPM Cancel Hari ini</p>
                         </div>
                         <div class="text-center py-3 mt-4">
                           <h1 class="mb-0 display-5 fw-bold">{{ number_format($totalDopmCancelHarian ?? 0) }} Cancel</h1>
                         </div>
                         <div class="text-center mt-3">
                           <p class="mb-0"><span class="text-success me-1">{{ number_format($totalDopmCancelHarian ?? 0) }}</span> Cancel pada hari ini</p>
                         </div>
                       </div>
                      </div>
                   </div>
                   <div class="col-12 col-xl-6 d-flex">
                    <div class="card mb-0 rounded-4 w-100">
                     <div class="card-body">
                       <div class="mb-2">
                         <h5 class="mb-0 fw-bold">Pekerjaan Batal</h5>
                         <p class="mb-0 text-muted small">Total IPK-IKK Status Batal Hari ini</p>
                       </div>
                       <div class="text-center py-3 mt-4">
                         <h1 class="mb-0 display-5 fw-bold">{{ number_format($totalPekerjaanBatalHarian ?? 0) }} Cancel</h1>
                       </div>
                       <div class="text-center mt-3">
                         <p class="mb-0 text-muted small">Data dari IPK-IKK</p>
                       </div>
                     </div>
                    </div>
                 </div>
                   <div class="col-12 col-xl-12">
                    <div class="card rounded-4 mb-0">
                      <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-2">
                           <div class="">
                             <h2 class="mb-0">{{ $pctPengisianRataRata ?? 0 }}%</h2>
                           </div>
                           <div class="">
                             <p class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-primary bg-opacity-10 text-primary"><span class="material-icons-outlined fs-6">trending_up</span>Rata-rata</p>
                           </div>
                         </div>
                         <p class="mb-0">Presentase Pengisian (IPK, OKK & OAK)</p>
                         <p class="mb-0 small text-muted">Rata-rata dari IPK {{ $pctDopmAdaIpk ?? 0 }}% · OKK {{ $pctDopmAdaOkk ?? 0 }}% · OAK {{ $pctDopmOak ?? 0 }}%</p>
                          <div class="mt-4">
                            <p class="mb-2 d-flex align-items-center justify-content-between">Gabungan IPK + OKK + OAK <span class="">{{ $pctPengisianRataRata ?? 0 }}%</span></p>
                            <div class="progress w-100" style="height: 7px;">
                              <div class="progress-bar bg-primary" style="width: {{ min(100, $pctPengisianRataRata ?? 0) }}%"></div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>

                 </div><!--end row-->
               </div>
            </div>  
          </div> 
          <div class="col-12 col-xl-7 col-xxl-8 d-flex">
            <div class="card w-100 rounded-4">
               <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">DOPM vs IPK-IKK vs OKK</h5>
                  </div>
                 </div>
                  <div id="chart4"></div>
                  <div class="d-flex flex-wrap align-items-center gap-3 border p-3 rounded-4 mt-3 text-center">
                    <span class="small text-muted ">Per jenis ijin kerja khusus (tanggal terpilih):</span>
                    <div class="d-flex align-items-center gap-2">
                      <span class="rounded-circle d-inline-block" style="width:12px;height:12px;background:#0d6efd;"></span>
                      <span class="small">DOPM</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <span class="rounded-circle d-inline-block" style="width:12px;height:12px;background:#02c27a;"></span>
                      <span class="small">IPK-IKK</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <span class="rounded-circle d-inline-block" style="width:12px;height:12px;background:#6f42c1;"></span>
                      <span class="small">OKK</span>
                    </div>
                  </div>
               </div>
            </div>  
          </div> 
        </div><!--end row-->

        <div class="row">
           <div class="col-12 col-xl-4 d-flex">
            <div class="card w-100 rounded-4">
               <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">Need Action</h5>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                 </div>
                  <div class="d-flex flex-column gap-4 dopm-matriks-list-scroll">
                    @php
                        $dopmMerah = collect($dopmListHarian ?? [])->where('status_matriks', 'Merah')->values();
                    @endphp
                    @forelse($dopmMerah as $dopm)
                     @php
                        $dopmJson = [
                            'kode_ikk' => $dopm->kode_ikk,
                            'jenis_ijin_kerja_khusus' => $dopm->jenis_ijin_kerja_khusus,
                            'sid_layer_2' => $dopm->sid_layer_2,
                            'sid_layer_3' => $dopm->sid_layer_3,
                            'sid_layer_4' => $dopm->sid_layer_4,
                            'nama_layer_2' => $dopm->nama_layer_2,
                            'nama_layer_3' => $dopm->nama_layer_3,
                            'nama_layer_4' => $dopm->nama_layer_4,
                            'nama_layer_1' => $dopm->nama_layer_1 ?? null,
                            'sid_layer_1' => $dopm->sid_layer_1 ?? null,
                            'id_dop' => $dopm->id_dop,
                            'nama_pekerjaan' => $dopm->nama_pekerjaan,
                            'site_ijin_kerja_khusus' => $dopm->site_ijin_kerja_khusus ?? null,
                            'perusahaan_ijin_kerja_khusus' => $dopm->perusahaan_ijin_kerja_khusus ?? null,
                            'tanggal_dop' => $dopm->tanggal_dop ? (\Carbon\Carbon::parse($dopm->tanggal_dop)->format('Y-m-d')) : null,
                            'timestamp' => $dopm->timestamp ? $dopm->timestamp->format('Y-m-d H:i') : null,
                            'status' => $dopm->status ?? null,
                        ];
                     @endphp
                     <div class="dopm-matriks-row d-flex align-items-center gap-4 rounded-3 p-2 border border-transparent hover-border cursor-pointer" role="button" tabindex="0" data-dopm="{{ json_encode($dopmJson) }}" title="Klik untuk detail DOPM, IPK-IKK, OKK, OAK">
                       <div class="d-flex align-items-center gap-3 flex-grow-1 flex-shrink-0 min-w-0">
                        <div class="wh-48 d-flex align-items-center justify-content-center rounded-3 border bg-danger bg-opacity-10 text-danger flex-shrink-0">
                          <span class="material-icons-outlined" style="font-size: 28px;">warning</span>
                        </div>
                          <div class="min-w-0">
                            <h6 class="mb-0 fw-bold text-truncate" title="{{ $dopm->id_dop ?? '-' }}">{{ $dopm->id_dop ?? '-' }}</h6>
                            <p class="mb-0 text-muted small text-truncate" title="{{ $dopm->kode_ikk ?? '-' }} • {{ $dopm->site_ijin_kerja_khusus ?? '-' }}">{{ $dopm->kode_ikk ?? '-' }} • {{ $dopm->site_ijin_kerja_khusus ?? '-' }}</p>
                          </div>
                       </div>
                       <div class="progress w-25 flex-shrink-0" style="height: 5px;">
                          <div class="progress-bar bg-danger" style="width: 0%"></div>
                       </div>
                       <div class="flex-shrink-0 d-flex align-items-center gap-2">
                       
                        <p class="mb-0 fs-6">0%</p>
                       </div>
                     </div>
                    @empty
                     <div class="text-center py-4 text-muted">
                        <span class="material-icons-outlined" style="font-size: 48px;">check_circle</span>
                        <p class="mb-0 mt-2 small">Tidak ada DOPM matriks Merah untuk tanggal ini.</p>
                     </div>
                    @endforelse
                  </div>
               </div>
             </div>
           </div>

           <div class="col-12 col-xl-4 d-flex">
            <div class="card w-100 rounded-4">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">Warning</h5>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                 </div>
                <div class="d-flex flex-column gap-4 dopm-matriks-list-scroll">
                  @php
                      $dopmKuning = collect($dopmListHarian ?? [])->where('status_matriks', 'Kuning')->values();
                  @endphp
                  @forelse($dopmKuning as $dopm)
                  @php
                    $dopmJsonK = [
                        'kode_ikk' => $dopm->kode_ikk,
                        'jenis_ijin_kerja_khusus' => $dopm->jenis_ijin_kerja_khusus,
                        'sid_layer_2' => $dopm->sid_layer_2,
                        'sid_layer_3' => $dopm->sid_layer_3,
                        'sid_layer_4' => $dopm->sid_layer_4,
                        'nama_layer_2' => $dopm->nama_layer_2,
                        'nama_layer_3' => $dopm->nama_layer_3,
                        'nama_layer_4' => $dopm->nama_layer_4,
                        'nama_layer_1' => $dopm->nama_layer_1 ?? null,
                        'sid_layer_1' => $dopm->sid_layer_1 ?? null,
                        'id_dop' => $dopm->id_dop,
                        'nama_pekerjaan' => $dopm->nama_pekerjaan,
                        'site_ijin_kerja_khusus' => $dopm->site_ijin_kerja_khusus ?? null,
                        'perusahaan_ijin_kerja_khusus' => $dopm->perusahaan_ijin_kerja_khusus ?? null,
                        'tanggal_dop' => $dopm->tanggal_dop ? (\Carbon\Carbon::parse($dopm->tanggal_dop)->format('Y-m-d')) : null,
                        'timestamp' => $dopm->timestamp ? $dopm->timestamp->format('Y-m-d H:i') : null,
                        'status' => $dopm->status ?? null,
                    ];
                  @endphp
                  <div class="dopm-matriks-row d-flex align-items-center gap-4 rounded-3 p-2 border border-transparent hover-border cursor-pointer" role="button" tabindex="0" data-dopm="{{ json_encode($dopmJsonK) }}" title="Klik untuk detail DOPM, IPK-IKK, OKK, OAK">
                    <div class="d-flex align-items-center gap-3 flex-grow-1 flex-shrink-0 min-w-0">
                      <div class="wh-48 d-flex align-items-center justify-content-center rounded-3 border bg-warning bg-opacity-10 text-warning flex-shrink-0">
                        <span class="material-icons-outlined" style="font-size: 28px;">info</span>
                      </div>
                      <div class="min-w-0">
                        <h6 class="mb-0 fw-bold text-truncate" title="{{ $dopm->id_dop ?? '-' }}">{{ $dopm->id_dop ?? '-' }}</h6>
                        <p class="mb-0 text-muted small text-truncate" title="{{ $dopm->kode_ikk ?? '-' }} • {{ $dopm->site_ijin_kerja_khusus ?? '-' }}">{{ $dopm->kode_ikk ?? '-' }} • {{ $dopm->site_ijin_kerja_khusus ?? '-' }}</p>
                      </div>
                    </div>
                    <div class="progress w-25 flex-shrink-0" style="height: 5px;">
                      <div class="progress-bar bg-warning text-dark" style="width: 50%"></div>
                    </div>
                    <div class="flex-shrink-0 d-flex align-items-center gap-2">
                     
                      <p class="mb-0 fs-6">50%</p>
                    </div>
                  </div>
                  @empty
                  <div class="text-center py-4 text-muted">
                    <span class="material-icons-outlined" style="font-size: 48px;">check_circle</span>
                    <p class="mb-0 mt-2 small">Tidak ada DOPM matriks Kuning untuk tanggal ini.</p>
                  </div>
                  @endforelse
                </div>
              </div>
            </div>  
          </div>

           <div class="col-12 col-xl-4 d-flex">
            <div class="card w-100 rounded-4">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">Complete</h5>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                 </div>
                <div class="d-flex flex-column gap-4 dopm-matriks-list-scroll">
                  @php
                      $dopmHijau = collect($dopmListHarian ?? [])->where('status_matriks', 'Hijau')->values();
                  @endphp
                  @forelse($dopmHijau as $dopm)
                  @php
                    $dopmJsonH = [
                        'kode_ikk' => $dopm->kode_ikk,
                        'jenis_ijin_kerja_khusus' => $dopm->jenis_ijin_kerja_khusus,
                        'sid_layer_2' => $dopm->sid_layer_2,
                        'sid_layer_3' => $dopm->sid_layer_3,
                        'sid_layer_4' => $dopm->sid_layer_4,
                        'nama_layer_2' => $dopm->nama_layer_2,
                        'nama_layer_3' => $dopm->nama_layer_3,
                        'nama_layer_4' => $dopm->nama_layer_4,
                        'nama_layer_1' => $dopm->nama_layer_1 ?? null,
                        'sid_layer_1' => $dopm->sid_layer_1 ?? null,
                        'id_dop' => $dopm->id_dop,
                        'nama_pekerjaan' => $dopm->nama_pekerjaan,
                        'site_ijin_kerja_khusus' => $dopm->site_ijin_kerja_khusus ?? null,
                        'perusahaan_ijin_kerja_khusus' => $dopm->perusahaan_ijin_kerja_khusus ?? null,
                        'tanggal_dop' => $dopm->tanggal_dop ? (\Carbon\Carbon::parse($dopm->tanggal_dop)->format('Y-m-d')) : null,
                        'timestamp' => $dopm->timestamp ? $dopm->timestamp->format('Y-m-d H:i') : null,
                        'status' => $dopm->status ?? null,
                    ];
                  @endphp
                  <div class="dopm-matriks-row d-flex align-items-center gap-4 rounded-3 p-2 border border-transparent hover-border cursor-pointer" role="button" tabindex="0" data-dopm="{{ json_encode($dopmJsonH) }}" title="Klik untuk detail DOPM, IPK-IKK, OKK, OAK">
                    <div class="d-flex align-items-center gap-3 flex-grow-1 flex-shrink-0 min-w-0">
                      <div class="wh-48 d-flex align-items-center justify-content-center rounded-3 border bg-success bg-opacity-10 text-success flex-shrink-0">
                        <span class="material-icons-outlined" style="font-size: 28px;">check_circle</span>
                      </div>
                      <div class="min-w-0">
                        <h6 class="mb-0 fw-bold text-truncate" title="{{ $dopm->id_dop ?? '-' }}">{{ $dopm->id_dop ?? '-' }}</h6>
                        <p class="mb-0 text-muted small text-truncate" title="{{ $dopm->kode_ikk ?? '-' }} • {{ $dopm->site_ijin_kerja_khusus ?? '-' }}">{{ $dopm->kode_ikk ?? '-' }} • {{ $dopm->site_ijin_kerja_khusus ?? '-' }}</p>
                      </div>
                    </div>
                    <div class="progress w-25 flex-shrink-0" style="height: 5px;">
                      <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                    <div class="flex-shrink-0 d-flex align-items-center gap-2">
                    
                      <p class="mb-0 fs-6">100%</p>
                    </div>
                  </div>
                  @empty
                  <div class="text-center py-4 text-muted">
                    <span class="material-icons-outlined" style="font-size: 48px;">inbox</span>
                    <p class="mb-0 mt-2 small">Tidak ada DOPM matriks Hijau untuk tanggal ini.</p>
                  </div>
                  @endforelse
                </div>
              </div>
            </div>
         </div>

         

    <div class="collapse show" id="dashboardStatsCollapse">
        {{-- <div class="row">
            <div class="col-12 d-flex">
                <div class="card rounded-4 w-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold">Ringkasan Data Harian</h5>
                            <span class="badge bg-primary">{{ \Carbon\Carbon::parse($filterDate ?? now())->locale('id')->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-around flex-wrap gap-4 p-4">
                            <a href="" class="btn p-0 border-0 bg-transparent d-flex flex-column align-items-center justify-content-center gap-2 text-decoration-none text-dark" title="Data DOPM tanggal terpilih">
                                <span class="mb-2 wh-48 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">assignment</i>
                                </span>
                                <h3 class="mb-0">{{ number_format($totalDopmHarian ?? 0) }}</h3>
                                <p class="mb-0">DOPM</p>
                                <small class="text-muted">Data Hari ini</small>
                            </a>
                            <div class="vr"></div>
                            <a href="" class="btn p-0 border-0 bg-transparent d-flex flex-column align-items-center justify-content-center gap-2 text-decoration-none text-dark" title="Presentase IKK yang ada IPK ({{ $ikkAdaIpkCount ?? 0 }}/{{ $totalIkkUnikHarian ?? 0 }} IKK)">
                                <span class="mb-2 wh-48 bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">checklist</i>
                                </span>
                                <h3 class="mb-0">{{ $pctIkkAdaIpk ?? 0 }}%</h3>
                                <p class="mb-0">IKK ada IPK</p>
                                <small class="text-muted">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaIpkCount ?? 0) }} belum IPK-IKK</small>
                            </a>
                            <div class="vr"></div>
                            <a href="" class="btn p-0 border-0 bg-transparent d-flex flex-column align-items-center justify-content-center gap-2 text-decoration-none text-dark" title="Presentase IKK yang ada OKK ({{ $ikkAdaOkkCount ?? 0 }}/{{ $totalIkkUnikHarian ?? 0 }} IKK)">
                                <span class="mb-2 wh-48 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">folder_open</i>
                                </span>
                                <h3 class="mb-0">{{ $pctIkkAdaOkk ?? 0 }}%</h3>
                                <p class="mb-0">IKK ada OKK</p>
                                <small class="text-muted">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaOkkCount ?? 0) }} belum OKK</small>
                            </a>
                            <div class="vr"></div>
                            <div class="d-flex flex-column align-items-center justify-content-center gap-2" title="OAK (Observasi Area Kerja) dari ClickHouse — tipe OBSERVE, layer 2/3/4 DOPM">
                                <span class="mb-2 wh-48 bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">visibility</i>
                                </span>
                                <h3 class="mb-0">{{ number_format($totalOakHarian ?? 0) }}</h3>
                                <p class="mb-0">OAK</p>
                                <small class="text-muted">Data Hari ini (OBSERVE, Layer 2/3/4)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- Summary harian per site: Jenis IJK & Status Matriks --}}
        {{-- @if(count($summaryBySite ?? []) > 0)
        <div class="row mt-3 g-3">
            <div class="col-12">
                <h5 class="mb-2 fw-bold d-flex align-items-center gap-2">
                    <span class="material-icons-outlined text-primary">summarize</span>
                    Ringkasan Ijin Kerja Khusus per Site — {{ \Carbon\Carbon::parse($filterDate ?? now())->locale('id')->translatedFormat('l, d F Y') }}
                </h5>
            </div>
            <div class="col-12 col-xl-7">
                <div class="card dopm-summary-card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex align-items-center gap-2">
                        <span class="material-icons-outlined text-primary" style="font-size: 1.25rem;">category</span>
                        Jumlah per Jenis IJK per Site
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover dopm-summary-table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Site</th>
                                        @foreach($summaryJenisKeys as $jk)
                                            <th class="text-center dopm-summary-jenis-col" title="{{ $jk }}">{{ strlen($jk) > 20 ? substr($jk, 0, 17) . '…' : $jk }}</th>
                                        @endforeach
                                        <th class="text-end fw-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summaryBySite as $site => $row)
                                        <tr>
                                            <td class="fw-semibold">{{ $site }}</td>
                                            @foreach($summaryJenisKeys as $jk)
                                                @php $cnt = $row['jenis'][$jk] ?? 0; @endphp
                                                <td class="text-center">{{ $cnt > 0 ? $cnt : '—' }}</td>
                                            @endforeach
                                            <td class="text-end dopm-summary-total">{{ $row['total'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-5">
                <div class="card dopm-summary-card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex align-items-center gap-2">
                        <span class="material-icons-outlined text-success" style="font-size: 1.25rem;">pie_chart</span>
                        Status Matriks per Site
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover dopm-summary-table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Site</th>
                                        <th class="text-center" style="background-color: #dcfce7;">Hijau</th>
                                        <th class="text-center" style="background-color: #fef9c3;">Kuning</th>
                                        <th class="text-center" style="background-color: #fee2e2;">Merah</th>
                                        <th class="text-end fw-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summaryBySite as $site => $row)
                                        @php
                                            $total = $row['total'] ?? 0;
                                            $h = $row['hijau'] ?? 0;
                                            $k = $row['kuning'] ?? 0;
                                            $m = $row['merah'] ?? 0;
                                            $pctH = $total > 0 ? round($h / $total * 100, 0) : 0;
                                            $pctK = $total > 0 ? round($k / $total * 100, 0) : 0;
                                            $pctM = $total > 0 ? round($m / $total * 100, 0) : 0;
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold">{{ $site }}</td>
                                            <td class="text-center dopm-summary-badge-hijau">{{ $pctH }}% <span class="d-block small">({{ $h }})</span></td>
                                            <td class="text-center dopm-summary-badge-kuning">{{ $pctK }}% <span class="d-block small">({{ $k }})</span></td>
                                            <td class="text-center dopm-summary-badge-merah">{{ $pctM }}% <span class="d-block small">({{ $m }})</span></td>
                                            <td class="text-end dopm-summary-total">100% <span class="d-block small">({{ $total }})</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif --}}

        {{-- Data DOPM harian (tampil langsung) --}}
        <div class="row mt-3">
            <div class="col-12 px-3 px-md-4">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">Data DOPM — {{ \Carbon\Carbon::parse($filterDate ?? now())->locale('id')->translatedFormat('l, d F Y') }}</h5>
                        <small class="text-muted">Klik Detail untuk melihat IPK-IKK, OKK, OAK per DOPM</small>
                    </div>
                    <div class="card-body p-0">
                        @if(count($dopmListHarian ?? []) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0 w-100" id="tableDopmHarian">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID DOP</th>
                                            <th>Kode IKK</th>
                                            <th>Site</th>
                                            <th>Jenis Ijin Kerja Khusus</th>
                                            <th>Nama Pekerjaan</th>
                                            <th>Perusahaan</th>
                                            <th>Status</th>
                                            <th>Status Matriks</th>
                                            <th>Nama Layer 1</th>
                                            <th>Layer 2 / 3 / 4</th>
                                            <th class="text-end">Intervensi</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dopmListHarian as $dopm)
                                            <tr>
                                                <td>{{ $dopm->id_dop ?? '-' }}</td>
                                                <td>{{ $dopm->kode_ikk ?? '-' }}</td>
                                                <td>{{ $dopm->site_ijin_kerja_khusus ?? '-' }}</td>
                                                <td>{{ $dopm->jenis_ijin_kerja_khusus ?? '-' }}</td>
                                                <td>{{ $dopm->nama_pekerjaan ?? '-' }}</td>
                                                <td>{{ $dopm->perusahaan_ijin_kerja_khusus ?? '-' }}</td>
                                                <td><span class="badge bg-secondary">{{ $dopm->status ?? '-' }}</span></td>
                                                <td>
                                                    @php
                                                        $matriks = $dopm->status_matriks ?? 'Merah';
                                                        $badgeClass = $matriks === 'Hijau' ? 'bg-success' : ($matriks === 'Kuning' ? 'bg-warning text-dark' : 'bg-danger');
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}" title="IKK ada IPK: {{ isset($dopm->is_ikk_ada_ipk) ? ($dopm->is_ikk_ada_ipk ? 'Ya' : 'Tidak') : '-' }}, IKK ada OKK: {{ isset($dopm->is_ikk_ada_okk) ? ($dopm->is_ikk_ada_okk ? 'Ya' : 'Tidak') : '-' }}">{{ $matriks }}</span>
                                                </td>
                                                <td><small class="text-primary">{{ $dopm->nama_layer_1 ?? '-' }}</small></td>
                                                <td><small>{{ $dopm->nama_layer_2 ?? '-' }} / {{ $dopm->nama_layer_3 ?? '-' }} / {{ $dopm->nama_layer_4 ?? '-' }}</small></td>
                                                <td class="text-end">
                                                    @php
                                                        $dopmJson = [
                                                            'kode_ikk' => $dopm->kode_ikk,
                                                            'jenis_ijin_kerja_khusus' => $dopm->jenis_ijin_kerja_khusus,
                                                            'sid_layer_2' => $dopm->sid_layer_2,
                                                            'sid_layer_3' => $dopm->sid_layer_3,
                                                            'sid_layer_4' => $dopm->sid_layer_4,
                                                            'nama_layer_2' => $dopm->nama_layer_2,
                                                            'nama_layer_3' => $dopm->nama_layer_3,
                                                            'nama_layer_4' => $dopm->nama_layer_4,
                                                            'nama_layer_1' => $dopm->nama_layer_1 ?? null,
                                                            'sid_layer_1' => $dopm->sid_layer_1 ?? null,
                                                            'id_dop' => $dopm->id_dop,
                                                            'nama_pekerjaan' => $dopm->nama_pekerjaan,
                                                            'site_ijin_kerja_khusus' => $dopm->site_ijin_kerja_khusus ?? null,
                                                            'perusahaan_ijin_kerja_khusus' => $dopm->perusahaan_ijin_kerja_khusus ?? null,
                                                            'tanggal_dop' => $dopm->tanggal_dop ? (\Carbon\Carbon::parse($dopm->tanggal_dop)->format('Y-m-d')) : null,
                                                            'timestamp' => $dopm->timestamp ? $dopm->timestamp->format('Y-m-d H:i') : null,
                                                            'status' => $dopm->status ?? null,
                                                        ];
                                                    @endphp
                                                    <button type="button" class="btn btn-sm btn-outline-warning btn-intervensi-dopm" data-dopm="{{ json_encode($dopmJson) }}" title="Intervensi (IPK-IKK, OKK, OAK)">
                                                        <i class="material-icons-outlined" style="font-size: 16px;">campaign</i> Intervensi
                                                    </button>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-detail-dopm" data-dopm="{{ json_encode($dopmJson) }}">
                                                        <i class="material-icons-outlined" style="font-size: 16px;">visibility</i> Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                                <p class="mb-0 mt-2">Tidak ada data DOPM untuk tanggal ini.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tabel IKK dari ClickHouse (ikk_work_permit) --}}
                <div class="card rounded-4 border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            Data IKK 
                            {{ \Carbon\Carbon::parse($filterDate ?? now())->locale('id')->translatedFormat('l, d F Y') }}
                        </h5>
                        <small class="text-muted">Data IKK harian dari tabel ClickHouse `hse_automation.ikk_work_permit`.</small>
                    </div>
                    <div class="card-body p-0">
                        {{-- Debug: kirim data IKK ClickHouse ke browser console --}}
                        <script>
                            console.log('ikkClickhouseListHarian (Blade)', @json($ikkClickhouseListHarian ?? []));
                            console.log('ikkClickhouseListHarian count', {{ count($ikkClickhouseListHarian ?? []) }});
                        </script>
                        @if(count($ikkClickhouseListHarian ?? []) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0 w-100" id="tableIkkClickhouseHarian">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Kode IKK</th>
                                            <th>Site</th>
                                            <th>Jenis Ijin Kerja Khusus</th>
                                            <th>Nama Pekerjaan</th>
                                            <th>Perusahaan</th>
                                            <th>Status</th>
                                            <th>Status Matriks</th>
                                            <th>Nama Layer 1</th>
                                            <th>Layer 2 / 3 / 4</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ikkClickhouseListHarian as $ikk)
                                            @php
                                                $matriksIkk = $ikk->status_matriks ?? 'Merah';
                                                $badgeClassIkk = $matriksIkk === 'Hijau'
                                                    ? 'bg-success'
                                                    : ($matriksIkk === 'Kuning' ? 'bg-warning text-dark' : 'bg-danger');
                                            @endphp
                                            <tr>
                                                <td>{{ $ikk->id ?? '-' }}</td>
                                                <td>{{ $ikk->code ?? '-' }}</td>
                                                <td>{{ $ikk->site ?? '-' }}</td>
                                                <td>{{ $ikk->jenis_ijin_kerja_khusus ?? '-' }}</td>
                                                <td>{{ $ikk->nama_pekerjaan ?? '-' }}</td>
                                                <td>{{ $ikk->perusahaan ?? '-' }}</td>
                                                <td><span class="badge bg-secondary">{{ $ikk->status ?? '-' }}</span></td>
                                                <td>
                                                    <span class="badge {{ $badgeClassIkk }}">{{ $matriksIkk }}</span>
                                                </td>
                                                <td><small class="text-primary">{{ $ikk->nama_layer_1 ?? '-' }}</small></td>
                                                <td>
                                                    <small>
                                                        {{ $ikk->nama_layer_2 ?? '-' }} /
                                                        {{ $ikk->nama_layer_3 ?? '-' }} /
                                                        {{ $ikk->nama_layer_4 ?? '-' }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                                <p class="mb-0 mt-2">Tidak ada data IKK dari ClickHouse untuk tanggal ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
    
          <div class="col-12 col-xl-5 col-xxl-4 d-flex">
            <div class="card rounded-4 w-100 shadow-none bg-transparent border-0">
               <div class="card-body p-0">
                 <div class="row g-4">
                    

              
                    <div class="col-12 col-xl-12">
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <h5 class="fw-bold mb-1">Akses Cepat Modul DOPM & IKK</h5>
                                    <small class="text-muted">Navigasi ke data DOPM, IPK-IKK, OKK</small>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mb-0">
                                    <a href="{{ route('dopmikk.dopm.index') }}" class="btn btn-primary rounded-3"><i class="material-icons-outlined me-1" style="font-size: 18px;">assignment</i> Data DOPM</a>
                                    <a href="{{ route('dopmikk.ipk-ikk.index') }}" class="btn btn-info rounded-3"><i class="material-icons-outlined me-1" style="font-size: 18px;">checklist</i> Data IPK-IKK</a>
                                    <a href="{{ route('dopmikk.okk.index') }}" class="btn btn-success rounded-3"><i class="material-icons-outlined me-1" style="font-size: 18px;">folder_open</i> Data OKK</a>
                                </div>
                                <p class="text-muted small mt-3 mb-0">OAK (Observasi Area Kerja) dari sistem eksternal; detail di Smart Alert Maps.</p>
                                <div class="accordion d-none" id="accordionAreaKritis">
                                    <!-- Jumlah Area Kritis -->
                                    <div class="accordion-item mb-3">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseJumlahAreaKritis">
                                                <div class="d-flex align-items-center gap-3 w-100">
                                                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                                                        <span class="material-icons-outlined text-danger">warning</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="mb-0 fw-bold" id="modalJumlahAreaKritis">0</h4>
                                                        <small class="text-muted">Jumlah Area Kritis</small>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseJumlahAreaKritis" class="accordion-collapse collapse" data-bs-parent="#accordionAreaKritis">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <strong>Penjelasan:</strong> Menampilkan total jumlah area yang dikategorikan sebagai area kritis. 
                                                    Area kritis adalah lokasi yang memiliki potensi bahaya tinggi dan memerlukan monitoring khusus 
                                                    untuk mencegah terjadinya insiden keselamatan kerja.
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total Area Kritis:</span>
                                                            <span class="badge bg-danger fs-6" id="detailJumlahAreaKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total Area Non Kritis:</span>
                                                            <span class="badge bg-success fs-6" id="detailJumlahAreaNonKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total Area:</span>
                                                            <span class="badge bg-primary fs-6" id="detailTotalArea">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3" id="listDetailAreaKritis">
                                                    <strong>Detail Area Kritis:</strong>
                                                    <div class="mt-2">
                                                        <small class="text-muted">Memuat data...</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Aktivitas Highrisk -->
                                    <div class="accordion-item mb-3">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCctvAreaKritis">
                                                <div class="d-flex align-items-center gap-3 w-100">
                                                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                                                        <span class="material-icons-outlined text-warning">warning</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="mb-0 fw-bold" id="modalCctvAreaKritis">0</h4>
                                                        <small class="text-muted">Aktivitas Highrisk</small>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseCctvAreaKritis" class="accordion-collapse collapse" data-bs-parent="#accordionAreaKritis">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <strong>Penjelasan:</strong> Menampilkan jumlah aktivitas highrisk yang tercatat di tabel cctv_coverage. 
                                                    Aktivitas highrisk adalah aktivitas dengan tingkat risiko tinggi yang memerlukan monitoring khusus 
                                                    untuk mencegah terjadinya insiden keselamatan kerja.
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Aktivitas Highrisk:</span>
                                                            <span class="badge bg-warning fs-6" id="detailCctvAreaKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total CCTV:</span>
                                                            <span class="badge bg-primary fs-6" id="detailTotalCctvAreaKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Persentase Aktivitas Highrisk:</span>
                                                            <span class="badge bg-info fs-6" id="detailPersentaseCctvAreaKritis">0%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3" id="listDetailAktivitasHighrisk">
                                                    <strong>Detail Lokasi Aktivitas Highrisk:</strong>
                                                    <div class="mt-2">
                                                        <small class="text-muted">Memuat data...</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CCTV Area Non Kritis -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCctvAreaNonKritis">
                                                <div class="d-flex align-items-center gap-3 w-100">
                                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                                        <span class="material-icons-outlined text-success">check_circle</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="mb-0 fw-bold" id="modalCctvAreaNonKritis">0</h4>
                                                        <small class="text-muted">CCTV Area Non Kritis</small>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseCctvAreaNonKritis" class="accordion-collapse collapse" data-bs-parent="#accordionAreaKritis">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <strong>Penjelasan:</strong> Menampilkan jumlah kamera CCTV yang terpasang di area non kritis. 
                                                    Area non kritis adalah lokasi dengan tingkat risiko rendah yang tetap dipantau untuk 
                                                    keamanan umum dan operasional sehari-hari.
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">CCTV di Area Non Kritis:</span>
                                                            <span class="badge bg-success fs-6" id="detailCctvAreaNonKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total CCTV:</span>
                                                            <span class="badge bg-primary fs-6" id="detailTotalCctvAreaNonKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Persentase Coverage Area Non Kritis:</span>
                                                            <span class="badge bg-info fs-6" id="detailPersentaseCctvAreaNonKritis">0%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                 
                 
                  
                   <div class="col-12 d-flex">
                      <div class="card mb-0 rounded-4 w-100">
                       <div class="card-body">
                         <h5 class="fw-bold mb-2">Ringkasan</h5>
                         <p class="text-muted mb-0">Dashboard ini menampilkan total rekor DOPM, IPK-IKK, OKK, dan OAK. Gunakan tombol di atas untuk membuka data masing-masing modul.</p>
                       </div>
                      </div>
                   </div>


                   

                 </div><!--end row-->
               </div>
            </div>  
          </div> 
          <div class="col-12 col-xl-7 col-xxl-8 d-flex">
            <div class="card w-100 rounded-4">
               <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">DOPM, IKK, OKK & OAK</h5>
                    <small class="text-muted">Statistik dan akses data modul DOPM$IKK</small>
                  </div>
                 </div>
                 {{-- <div class="table-responsive">
                   <table class="table table-hover align-middle mb-4" id="coverageTable">
                     <tbody id="coverageTableBody">
                       <tr>
                         <td colspan="5" class="text-center text-muted py-4">
                           <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                           Memuat data...
                         </td>
                       </tr>
                     </tbody>
                   </table>
                 </div> --}}
                  {{-- <div class="d-flex flex-column flex-lg-row align-items-start justify-content-around border p-3 rounded-4 mt-3 gap-3">
                   
                    <div class="d-flex align-items-center gap-4 cctv-stat-card" id="cctvStatCard" style="cursor: pointer; padding: 8px; border-radius: 8px; transition: all 0.2s;" 
                         title="Klik untuk melihat detail TBC">
                      <div class="">
                        <p class="mb-0 data-attributes">
                          <span id="donutCctv"
                            data-peity='{ "fill": ["#6f42c1", "rgb(0 0 0 / 10%)"], "innerRadius": 32, "radius": 40 }'>0/100</span>
                        </p>
                      </div>
                      <div class="">
                        <p class="mb-1 fs-6 fw-bold">TBC</p>
                        <h2 class="mb-0" id="statCctvCount">0</h2>
                        <p class="mb-0"><span class="text-success me-2 fw-medium" id="statCctvChange">0 %</span><span id="statCctvText"> valid TBC</span></p>
                      </div>
                    </div>
                     <div class="vr"></div>
                    <div class="d-flex align-items-center gap-4">
                      <div class="">
                        <p class="mb-0 data-attributes">
                          <span id="donutHazard"
                            data-peity='{ "fill": ["#0d6efd", "rgb(0 0 0 / 10%)"], "innerRadius": 32, "radius": 40 }'>0/100</span>
                        </p>
                      </div>
                      <div class="">
                        <p class="mb-1 fs-6 fw-bold">HAZARD</p>
                        <h2 class="mb-0" id="statHazardCount">{{ number_format($monthlyHazards ?? 65) }}</h2>
                        <p class="mb-0"><span class="text-success me-2 fw-medium" id="statHazardChange">{{ $monthlyChange ?? '16.5' }}%</span><span id="statHazardText">{{ $monthlyCount ?? '55' }} hazards</span></p>
                      </div>
                    </div>
                   
                    

                      <div class="vr"></div>
                    <div class="d-flex align-items-center gap-4">
                      <div class="">
                        <p class="mb-0 data-attributes">
                          <span id="donutInsiden"
                            data-peity='{ "fill": ["#fd7e14", "rgb(0 0 0 / 10%)"], "innerRadius": 32, "radius": 40 }'>0/100</span>
                        </p>
                      </div>
                      <div class="">
                        <p class="mb-1 fs-6 fw-bold">INSIDEN</p>
                        <h2 class="mb-0" id="statInsidenCount">{{ number_format($yearlyHazards ?? 9) }}</h2>
                        <p class="mb-0"><span class="text-success me-2 fw-medium" id="statInsidenChange">{{ $yearlyChange ?? '24.9' }}%</span><span id="statInsidenText">{{ $yearlyCount ?? '267' }} hazards</span></p>
                      </div>
                    </div>

                      <div class="vr"></div>
                    <div class="d-flex align-items-center gap-4">
                      <div class="">
                        <p class="mb-0 data-attributes">
                          <span id="donutGr"
                            data-peity='{ "fill": ["#20c997", "rgb(0 0 0 / 10%)"], "innerRadius": 32, "radius": 40 }'>0/100</span>
                        </p>
                      </div>
                      <div class="">
                        <p class="mb-1 fs-6 fw-bold">GR</p>
                        <h2 class="mb-0" id="statGrCount">{{ number_format($validGrCount ?? 0) }}</h2>
                        <p class="mb-0"><span class="text-success me-2 fw-medium" id="statGrChange">{{ $yearlyChange ?? '24.9' }}%</span><span id="statGrText">{{ $yearlyCount ?? '267' }} hazards</span></p>
                      </div>
                    </div>

                    
                  </div> --}}

                 
                  <div class="border p-3 rounded-4 mt-3">
                    <p class="mb-0 text-muted">Gunakan kartu statistik di atas untuk melihat total DOPM, IPK-IKK, OKK, dan OAK. Klik setiap kartu untuk membuka daftar data.</p>
                  </div>

                    
                  </div>
               </div>
            </div>  
          </div> 
</div><!--end row-->
    </div><!--end collapse-->





</div>



    {{-- Modal Detail DOPM: Menampilkan IPK-IKK, OKK, OAK dalam satu tampilan tanpa tab --}}
    <div class="modal fade" id="detailDopmModal" tabindex="-1" aria-labelledby="detailDopmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border border-light">
                <div class="modal-header rounded-top-4 py-3">
                    <div class="d-flex align-items-center flex-grow-1">
                        <span class="material-icons-outlined me-2 fs-4 text-primary">assignment</span>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="detailDopmModalLabel">
                                <span id="detailDopmTitle">Detail DOPM</span>
                            </h5>
                            <small class="text-muted" id="detailDopmSubtitle">Kode IKK: —</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Statistik IPK-IKK, OKK, OAK --}}
                <div class="modal-stat-cards px-4 py-3">
                    <p class="small fw-semibold text-muted mb-2">Statistik</p>
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="stat-card p-3 h-100">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-icons-outlined text-primary">checklist</span>
                                    <div>
                                        <span class="d-block fw-bold text-dark fs-5" id="statCountIpkIkk">0</span>
                                        <span class="small text-muted">IPK-IKK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="stat-card p-3 h-100">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-icons-outlined text-success">folder_open</span>
                                    <div>
                                        <span class="d-block fw-bold text-dark fs-5" id="statCountOkk">0</span>
                                        <span class="small text-muted">OKK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="stat-card p-3 h-100">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-icons-outlined text-warning">visibility</span>
                                    <div>
                                        <span class="d-block fw-bold text-dark fs-5" id="statCountOak">0</span>
                                        <span class="small text-muted">OAK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div class="p-4 bg-white" id="detailDopmContent" style="max-height: 600px; overflow-y: auto;">
                        {{-- Data DOPM (dari database) --}}
                        <div class="card border mb-4 bg-light">
                            <div class="card-body py-3">
                                <h6 class="fw-bold mb-3 text-dark"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">description</i> Data DOPM</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-striped align-middle mb-0 small">
                                        <tbody>
                                            <tr><td class="text-muted fw-semibold" style="width: 140px;">ID DOP</td><td id="detailDopmId">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Kode IKK</td><td id="detailDopmKodeIkk">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Nama Pekerjaan</td><td id="detailDopmNamaPekerjaan" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Site</td><td id="detailDopmSite">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Perusahaan</td><td id="detailDopmPerusahaan" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Jenis IJK</td><td id="detailDopmJenisIjk" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Tanggal DOP</td><td id="detailDopmTanggal">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Status</td><td id="detailDopmStatus">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Layer 1</td><td id="detailDopmLayer1">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Layer 2</td><td id="detailDopmLayer2">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Layer 3</td><td id="detailDopmLayer3">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Layer 4</td><td id="detailDopmLayer4">—</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-warning btn-sm btn-intervensi-from-detail" id="btnIntervensiFromDetailTop" title="Intervensi (IPK-IKK, OKK, OAK)">
                                        <i class="material-icons-outlined align-middle me-1" style="font-size: 16px;">campaign</i> Intervensi
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- Section IPK-IKK --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="material-icons-outlined text-primary me-2" style="font-size: 20px;">checklist</i>
                                <h6 class="mb-0 fw-bold">IPK-IKK <span class="badge bg-primary ms-2" id="badgeIpkIkk">0</span></h6>
                            </div>
                            <div id="ipkIkkLoading" class="text-center py-4 d-none bg-white">
                                <div class="spinner-border text-primary mb-2" role="status"></div>
                                <p class="text-muted mb-0">Memuat data IPK-IKK...</p>
                            </div>
                            <div id="ipkIkkEmpty" class="text-center py-4 d-none bg-white">
                                <span class="material-icons-outlined text-muted" style="font-size: 48px;">inbox</span>
                                <p class="text-muted mt-2 mb-0">Tidak ada data IPK-IKK untuk kode IKK ini.</p>
                            </div>
                            <div id="ipkIkkTableWrap" class="d-none bg-white">
                                <p class="small text-muted mb-2">Tabel di bawah menampilkan seluruh data IPK-IKK dengan kode IKK ini.</p>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="tableIpkIkk">
                                        <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Durasi</th><th>CCTV</th><th>Kategori IJK</th><th>Status</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Section OKK --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="material-icons-outlined text-success me-2" style="font-size: 20px;">folder_open</i>
                                <h6 class="mb-0 fw-bold">OKK <span class="badge bg-success ms-2" id="badgeOkk">0</span></h6>
                            </div>
                            <div id="okkLoading" class="text-center py-4 d-none bg-white">
                                <div class="spinner-border text-success mb-2" role="status"></div>
                                <p class="text-muted mb-0">Memuat data OKK...</p>
                            </div>
                            <div id="okkEmpty" class="text-center py-4 d-none bg-white">
                                <span class="material-icons-outlined text-muted" style="font-size: 48px;">inbox</span>
                                <p class="text-muted mt-2 mb-0">Tidak ada data OKK untuk kode IKK ini.</p>
                            </div>
                            <div id="okkTableWrap" class="d-none bg-white">
                                <p class="small text-muted mb-2">Tabel di bawah menampilkan seluruh data OKK dengan kode IKK ini.</p>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="tableOkk">
                                        <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Jenis IJK</th><th>Layer</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Section OAK --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="material-icons-outlined text-warning me-2" style="font-size: 20px;">visibility</i>
                                <h6 class="mb-0 fw-bold">OAK <span class="badge bg-warning text-dark ms-2" id="badgeOak">0</span></h6>
                            </div>
                            <div id="oakContext" class="card border mb-3 d-none bg-white">
                                <div class="card-body py-2 px-3">
                                    <small class="text-muted fw-semibold">Layer 2 / 3 / 4:</small>
                                    <span id="oakLayerNames" class="ms-1">—</span>
                                </div>
                            </div>
                            <div id="oakLoading" class="text-center py-4 d-none bg-white">
                                <div class="spinner-border text-warning mb-2" role="status"></div>
                                <p class="text-muted mb-0">Memuat data OAK...</p>
                            </div>
                            <div id="oakEmpty" class="text-center py-4 d-none bg-white">
                                <span class="material-icons-outlined text-muted" style="font-size: 48px;">inbox</span>
                                <p class="text-muted mt-2 mb-0">Tidak ada data OAK untuk kriteria ini.</p>
                            </div>
                            <div id="oakTableWrap" class="d-none bg-white">
                                <p class="small text-muted mb-2">Tabel di bawah menampilkan data Observasi Area Kerja sesuai activity dan SID.</p>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="tableOak">
                                        <thead class="table-light"><tr><th>Activity</th><th>Sub Activity</th><th>Submit Date</th><th>Submit By</th><th>SID Pelapor</th><th>SID Team</th><th>Conclusion</th><th>Site</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-warning btn-intervensi-from-detail" id="btnIntervensiFromDetail" title="Intervensi (IPK-IKK, OKK, OAK)">
                        <i class="material-icons-outlined align-middle me-1" style="font-size: 18px;">campaign</i> Intervensi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Intervensi DOPM: satu modal, 3 section (IPK-IKK, OKK, OAK) + Layer masing-masing, tanpa tab --}}
    <div class="modal fade" id="intervensiDopmModal" tabindex="-1" aria-labelledby="intervensiDopmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg border border-light">
                <div class="modal-header rounded-top-4 py-3 bg-warning bg-opacity-10">
                    <div class="d-flex align-items-center flex-grow-1">
                        <span class="material-icons-outlined me-2 fs-4 text-warning">campaign</span>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="intervensiDopmModalLabel">
                                <span id="intervensiDopmTitle">Intervensi DOPM</span>
                            </h5>
                            <small class="text-muted" id="intervensiDopmSubtitle">Kode IKK: —</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    {{-- Section 1: IPK-IKK + Layer 1 --}}
                    <div class="intervensi-section mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">checklist</i> IPK-IKK <span class="badge bg-primary ms-1" id="intervensiBadgeIpk">0</span></h6>
                        <div id="intervensiLayer1Wrap" class="card border-warning mb-3">
                            <div class="card-header bg-warning bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-warning">notifications_active</span>
                                <strong>Layer 1 — Pengingat Isi IPK (INSPEKSI PRA KERJA)</strong>
                            </div>
                            <div class="card-body py-3">
                                <p class="small mb-2"><strong>Nama Layer:</strong> <span id="intervensiLayer1NameDisplay" class="text-dark">—</span></p>
                                <p class="small text-muted mb-2">Kirim pengingat WA ke PIC Layer 1 untuk mengisi form IPK:</p>
                                <div id="intervensiLayer1Users" class="d-flex flex-wrap gap-2"></div>
                                <div id="intervensiLayer1Empty" class="text-muted small d-none">Tidak ada user terdaftar untuk Layer 1 ini.</div>
                                <div id="intervensiLayer1NoName" class="text-muted small d-none">Kolom <strong>SID Layer 1</strong> atau <strong>Nama Layer 1</strong> untuk DOPM ini belum diisi. Silakan edit data DOPM untuk menampilkan daftar PIC dan tombol Intervensi by WA.</div>
                                <div id="intervensiLayer1Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Memuat daftar PIC Layer 1...</div>
                            </div>
                        </div>
                        <div id="intervensiIpkLoading" class="text-center py-3 d-none"><div class="spinner-border text-primary spinner-border-sm" role="status"></div><p class="text-muted mb-0 mt-2 small">Memuat data IPK-IKK...</p></div>
                        <div id="intervensiIpkEmpty" class="text-center py-3 d-none"><span class="material-icons-outlined text-muted" style="font-size: 32px;">inbox</span><p class="text-muted mt-2 mb-0 small">Tidak ada data IPK-IKK.</p></div>
                        <div id="intervensiIpkTableWrap" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableIpk">
                                    <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Durasi</th><th>CCTV</th><th>Kategori IJK</th><th>Status</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: OKK + Layer 1 --}}
                    <div class="intervensi-section mb-4">
                        <h6 class="text-success border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">folder_open</i> OKK <span class="badge bg-success ms-1" id="intervensiBadgeOkk">0</span></h6>
                        <div id="intervensiOkkLayer1Wrap" class="card border-success mb-3">
                            <div class="card-header bg-success bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-success">notifications_active</span>
                                <strong>Layer 1 — Intervensi OKK (OBSERVASI KEGIATAN KERJA)</strong>
                            </div>
                            <div class="card-body py-3">
                                <p class="small mb-2"><strong>Nama Layer:</strong> <span id="intervensiOkkLayer1NameDisplay" class="text-dark">—</span></p>
                                <p class="small text-muted mb-2">Kirim pengingat WA ke PIC Layer 1 untuk OKK:</p>
                                <div id="intervensiOkkLayer1Users" class="d-flex flex-wrap gap-2"></div>
                                <div id="intervensiOkkLayer1Empty" class="text-muted small d-none">Tidak ada user terdaftar untuk Layer 1 ini.</div>
                                <div id="intervensiOkkLayer1NoName" class="text-muted small d-none">Kolom <strong>SID Layer 1</strong> atau <strong>Nama Layer 1</strong> untuk DOPM ini belum diisi.</div>
                                <div id="intervensiOkkLayer1Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Memuat daftar PIC Layer 1...</div>
                            </div>
                        </div>
                        <div id="intervensiOkkLoading" class="text-center py-3 d-none"><div class="spinner-border text-success spinner-border-sm" role="status"></div><p class="text-muted mb-0 mt-2 small">Memuat data OKK...</p></div>
                        <div id="intervensiOkkEmpty" class="text-center py-3 d-none"><span class="material-icons-outlined text-muted" style="font-size: 32px;">inbox</span><p class="text-muted mt-2 mb-0 small">Tidak ada data OKK.</p></div>
                        <div id="intervensiOkkTableWrap" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableOkk">
                                    <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Jenis IJK</th><th>Layer</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: OAK + Intervensi Layer 2, 3, 4 --}}
                    <div class="intervensi-section">
                        <h6 class="text-warning text-dark border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">visibility</i> OAK <span class="badge bg-warning text-dark ms-1" id="intervensiBadgeOak">0</span></h6>
                        {{-- Intervensi OAK: Layer 2, Layer 3, Layer 4 — 3 tombol intervensi by WA --}}
                        <div id="intervensiOakLayersWrap" class="card border-warning mb-3">
                            <div class="card-header bg-warning bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-warning">notifications_active</span>
                                <strong>Intervensi OAK — Layer 2, 3, 4</strong>
                            </div>
                            <div class="card-body py-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small mb-1 fw-semibold">Layer 2</p>
                                            <p class="small mb-1 text-muted"><strong>Nama:</strong> <span id="intervensiOakLayer2Name" class="text-dark">—</span></p>
                                            <div id="intervensiOakLayer2Users" class="d-flex flex-wrap gap-1"></div>
                                            <div id="intervensiOakLayer2Empty" class="text-muted small d-none">Tidak ada user.</div>
                                            <div id="intervensiOakLayer2Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small mb-1 fw-semibold">Layer 3</p>
                                            <p class="small mb-1 text-muted"><strong>Nama:</strong> <span id="intervensiOakLayer3Name" class="text-dark">—</span></p>
                                            <div id="intervensiOakLayer3Users" class="d-flex flex-wrap gap-1"></div>
                                            <div id="intervensiOakLayer3Empty" class="text-muted small d-none">Tidak ada user.</div>
                                            <div id="intervensiOakLayer3Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small mb-1 fw-semibold">Layer 4</p>
                                            <p class="small mb-1 text-muted"><strong>Nama:</strong> <span id="intervensiOakLayer4Name" class="text-dark">—</span></p>
                                            <div id="intervensiOakLayer4Users" class="d-flex flex-wrap gap-1"></div>
                                            <div id="intervensiOakLayer4Empty" class="text-muted small d-none">Tidak ada user.</div>
                                            <div id="intervensiOakLayer4Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="intervensiOakLoading" class="text-center py-3 d-none"><div class="spinner-border text-warning spinner-border-sm" role="status"></div><p class="text-muted mb-0 mt-2 small">Memuat data OAK...</p></div>
                        <div id="intervensiOakEmpty" class="text-center py-3 d-none"><span class="material-icons-outlined text-muted" style="font-size: 32px;">inbox</span><p class="text-muted mt-2 mb-0 small">Tidak ada data OAK.</p></div>
                        <div id="intervensiOakTableWrap" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableOak">
                                    <thead class="table-light"><tr><th>Activity</th><th>Sub Activity</th><th>Submit Date</th><th>Submit By</th><th>SID Pelapor</th><th>SID Team</th><th>Conclusion</th><th>Site</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>







@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ol@8.2.0/dist/ol.js"></script>
<script src="https://cdn.jsdelivr.net/npm/proj4@2.9.0/dist/proj4.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.7/dist/hls.min.js"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<!-- Load BMO2 PAMA GeoJSON data -->


<script src="{{ URL::asset('build/plugins/apexchart/apexcharts.min.js') }}"></script>
<script>window.skipChart4 = true;</script>
<script src="{{ URL::asset('build/js/index.js') }}"></script>
<script src="{{ URL::asset('build/plugins/peity/jquery.peity.min.js') }}"></script>
<script>
(function() {
  var categories = @json($chartJenisLabels ?? []);
  var dopmData = @json($chartDopmPerJenis ?? []);
  var ipkData = @json($chartIpkPerJenis ?? []);
  var okkData = @json($chartOkkPerJenis ?? []);
  setTimeout(function() {
    var el = document.querySelector('#chart4');
    if (!el || typeof ApexCharts === 'undefined') return;
    try { ApexCharts.exec('chart4', 'destroy'); } catch (e) {}
    el.innerHTML = '';
    new ApexCharts(el, {
      chart: { id: 'chart4', height: 235, type: 'bar', toolbar: { show: false }, fontFamily: 'inherit' },
      plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
      dataLabels: { enabled: false },
      stroke: { show: true, width: 2, colors: ['transparent'] },
      series: [
        { name: 'DOPM', data: dopmData },
        { name: 'IPK-IKK', data: ipkData },
        { name: 'OKK', data: okkData }
      ],
      xaxis: { categories: categories, labels: { style: { colors: '#a1acb8' } } },
      yaxis: { labels: { style: { colors: '#a1acb8' } } },
      colors: ['#0d6efd', '#02c27a', '#6f42c1'],
      grid: { borderColor: 'rgba(0,0,0,0.05)', strokeDashArray: 4 },
      legend: { show: true, position: 'top', horizontalAlign: 'right' },
      tooltip: { y: { formatter: function(v) { return v; } } }
    }).render();
  }, 150);
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    // Loading SweetAlert saat submit filter tanggal
    var filterForm = document.getElementById('dashboardFilterForm');
    if (filterForm && typeof Swal !== 'undefined') {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Memuat data...',
                html: 'Menampilkan data untuk tanggal yang dipilih.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });
            filterForm.submit();
        });
    }

    var modalApiUrl = @json(route('dopmikk.api.ikk-modal-data'));
    var layer1UsersApiUrl = @json(route('dopmikk.api.layer1-users'));
    var layers234UsersApiUrl = @json(route('dopmikk.api.layers234-users'));
    var ipkFormLink = 'https://docs.google.com/forms/d/e/1FAIpQLSddTpsj6qbXN3pSpHvhZvPJdM4CU10H3oY9k3MEg6NTzRublA/viewform';
    var modalEl = document.getElementById('detailDopmModal');
    var intervensiModalEl = document.getElementById('intervensiDopmModal');
    var intervensiModal = intervensiModalEl ? new bootstrap.Modal(intervensiModalEl) : null;
    if (!modalEl) return;
    var modal = new bootstrap.Modal(modalEl);

    // DataTables untuk tabel DOPM harian
    if ($.fn.DataTable && document.getElementById('tableDopmHarian')) {
        $('#tableDopmHarian').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ baris',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(filter dari _MAX_ data)',
                paginate: { first: 'Awal', last: 'Akhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            },
            columnDefs: [{ targets: [9, 10], orderable: false }],
            dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    }

    // DataTables untuk tabel IKK ClickHouse harian
    if ($.fn.DataTable && document.getElementById('tableIkkClickhouseHarian')) {
        $('#tableIkkClickhouseHarian').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ baris',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(filter dari _MAX_ data)',
                paginate: { first: 'Awal', last: 'Akhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            },
            dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    }

    function showLoading(panel) {
        var loadingEl = document.getElementById(panel + 'Loading');
        var emptyEl = document.getElementById(panel + 'Empty');
        var wrapEl = document.getElementById(panel + 'TableWrap');
        if (loadingEl) { loadingEl.classList.remove('d-none'); loadingEl.style.display = 'block'; }
        if (emptyEl) { emptyEl.classList.add('d-none'); emptyEl.style.display = 'none'; }
        if (wrapEl) { wrapEl.classList.add('d-none'); wrapEl.style.display = 'none'; }
    }
    function showEmpty(panel) {
        var loadingEl = document.getElementById(panel + 'Loading');
        var emptyEl = document.getElementById(panel + 'Empty');
        var wrapEl = document.getElementById(panel + 'TableWrap');
        if (loadingEl) { loadingEl.classList.add('d-none'); loadingEl.style.display = 'none'; }
        if (emptyEl) { emptyEl.classList.remove('d-none'); emptyEl.style.display = 'block'; }
        if (wrapEl) { wrapEl.classList.add('d-none'); wrapEl.style.display = 'none'; }
    }
    function showTable(panel) {
        var loadingEl = document.getElementById(panel + 'Loading');
        var emptyEl = document.getElementById(panel + 'Empty');
        var wrapEl = document.getElementById(panel + 'TableWrap');
        if (loadingEl) { loadingEl.classList.add('d-none'); loadingEl.style.display = 'none'; }
        if (emptyEl) { emptyEl.classList.add('d-none'); emptyEl.style.display = 'none'; }
        if (wrapEl) { wrapEl.classList.remove('d-none'); wrapEl.style.display = 'block'; }
    }

    function tr(cells) {
        var row = document.createElement('tr');
        cells.forEach(function(c) {
            var td = document.createElement('td');
            td.textContent = c == null || c === undefined ? '—' : String(c);
            row.appendChild(td);
        });
        return row;
    }
    function safeStr(val, maxLen) {
        if (val == null || val === undefined) return '—';
        var s = String(val).trim();
        if (!s) return '—';
        if (maxLen && s.length > maxLen) s = s.substring(0, maxLen);
        return s;
    }
    function formatTs(ts) {
        if (!ts) return '—';
        var s = String(ts).trim();
        if (!s) return '—';
        var m = s.match(/^(\d{4})-(\d{2})-(\d{2})[T\s](\d{2}):(\d{2})/);
        if (m) return m[3] + '/' + m[2] + '/' + m[1] + ' ' + m[4] + ':' + m[5];
        return s;
    }


    // Normalisasi nomor untuk wa.me: 08xxx -> 62xxx
    function normalizeWaNumber(selular) {
        if (!selular || typeof selular !== 'string') return '';
        var s = selular.replace(/\s+/g, '').replace(/-/g, '');
        if (/^0\d+/.test(s)) return '62' + s.substring(1);
        if (!/^62/.test(s) && /^\d+/.test(s)) return '62' + s;
        return s;
    }

    // Event delegation: tombol Intervensi
    document.addEventListener('click', function(e) {
        var btnIntervensi = e.target.closest('.btn-intervensi-dopm');
        if (btnIntervensi && intervensiModal) {
            var data = JSON.parse(btnIntervensi.getAttribute('data-dopm') || '{}');
            var namaLayer1 = (data.nama_layer_1 || '').trim();
            var sidLayer1 = (data.sid_layer_1 || '').trim();
            var hasLayer1 = sidLayer1 !== '' || namaLayer1 !== '';
            document.getElementById('intervensiDopmTitle').textContent = (data.id_dop || 'Intervensi') + ' — ' + (data.nama_pekerjaan || 'DOPM').substring(0, 50);
            document.getElementById('intervensiDopmSubtitle').textContent = 'Kode IKK: ' + (data.kode_ikk || '—');
            document.getElementById('intervensiBadgeIpk').textContent = '0';
            document.getElementById('intervensiBadgeOkk').textContent = '0';
            document.getElementById('intervensiBadgeOak').textContent = '0';
            document.getElementById('intervensiIpkLoading').classList.remove('d-none');
            document.getElementById('intervensiIpkEmpty').classList.add('d-none');
            document.getElementById('intervensiIpkTableWrap').classList.add('d-none');
            document.getElementById('intervensiOkkLoading').classList.remove('d-none');
            document.getElementById('intervensiOkkEmpty').classList.add('d-none');
            document.getElementById('intervensiOkkTableWrap').classList.add('d-none');
            document.getElementById('intervensiOakLoading').classList.remove('d-none');
            document.getElementById('intervensiOakEmpty').classList.add('d-none');
            document.getElementById('intervensiOakTableWrap').classList.add('d-none');
            [2, 3, 4].forEach(function(n) {
                var usersEl = document.getElementById('intervensiOakLayer' + n + 'Users');
                var emptyEl = document.getElementById('intervensiOakLayer' + n + 'Empty');
                var loadingEl = document.getElementById('intervensiOakLayer' + n + 'Loading');
                var nameEl = document.getElementById('intervensiOakLayer' + n + 'Name');
                if (usersEl) usersEl.innerHTML = '';
                if (emptyEl) { emptyEl.classList.add('d-none'); }
                if (loadingEl) { loadingEl.classList.remove('d-none'); }
                if (nameEl) nameEl.textContent = '—';
            });
            var layer1Wrap = document.getElementById('intervensiLayer1Wrap');
            var layer1UsersEl = document.getElementById('intervensiLayer1Users');
            var layer1EmptyEl = document.getElementById('intervensiLayer1Empty');
            var layer1NoNameEl = document.getElementById('intervensiLayer1NoName');
            var layer1LoadingEl = document.getElementById('intervensiLayer1Loading');
            var okkLayer1Wrap = document.getElementById('intervensiOkkLayer1Wrap');
            var okkLayer1NameDisplay = document.getElementById('intervensiOkkLayer1NameDisplay');
            var okkLayer1UsersEl = document.getElementById('intervensiOkkLayer1Users');
            var okkLayer1EmptyEl = document.getElementById('intervensiOkkLayer1Empty');
            var okkLayer1NoNameEl = document.getElementById('intervensiOkkLayer1NoName');
            var okkLayer1LoadingEl = document.getElementById('intervensiOkkLayer1Loading');
            layer1Wrap.classList.remove('d-none');
            layer1UsersEl.innerHTML = '';
            document.getElementById('intervensiLayer1NameDisplay').textContent = namaLayer1 || '—';
            layer1EmptyEl.classList.add('d-none');
            layer1NoNameEl.classList.add('d-none');
            layer1LoadingEl.classList.add('d-none');
            if (okkLayer1Wrap) { okkLayer1Wrap.classList.remove('d-none'); okkLayer1UsersEl.innerHTML = ''; okkLayer1NameDisplay.textContent = namaLayer1 || '—'; okkLayer1EmptyEl.classList.add('d-none'); okkLayer1NoNameEl.classList.add('d-none'); okkLayer1LoadingEl.classList.add('d-none'); }
            if (!hasLayer1) {
                layer1NoNameEl.classList.remove('d-none');
                if (okkLayer1NoNameEl) okkLayer1NoNameEl.classList.remove('d-none');
            } else {
                layer1LoadingEl.classList.remove('d-none');
                if (okkLayer1LoadingEl) okkLayer1LoadingEl.classList.remove('d-none');
            }
            intervensiModal.show();

            var params = new URLSearchParams({
                kode_ikk: data.kode_ikk || '',
                jenis_ijin_kerja_khusus: data.jenis_ijin_kerja_khusus || '',
                sid_layer_2: data.sid_layer_2 || '',
                sid_layer_3: data.sid_layer_3 || '',
                sid_layer_4: data.sid_layer_4 || '',
                nama_layer_2: data.nama_layer_2 || '',
                nama_layer_3: data.nama_layer_3 || '',
                nama_layer_4: data.nama_layer_4 || ''
            });

            function doIntervensiFetch() {
                fetch(modalApiUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(function(res) {
                        if (!res || !res.success) throw new Error('Request failed');
                        var ipk = res.ipk_ikk || [], okk = res.okk || [], oak = res.oak || [];
                        document.getElementById('intervensiBadgeIpk').textContent = ipk.length;
                        document.getElementById('intervensiBadgeOkk').textContent = okk.length;
                        document.getElementById('intervensiBadgeOak').textContent = oak.length;
                        document.getElementById('intervensiIpkLoading').classList.add('d-none');
                        document.getElementById('intervensiOkkLoading').classList.add('d-none');
                        document.getElementById('intervensiOakLoading').classList.add('d-none');
                        if (ipk.length === 0) { document.getElementById('intervensiIpkEmpty').classList.remove('d-none'); document.getElementById('intervensiIpkTableWrap').classList.add('d-none'); } else {
                            document.getElementById('intervensiIpkEmpty').classList.add('d-none');
                            document.getElementById('intervensiIpkTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableIpk tbody');
                            if (tbody) { tbody.innerHTML = ''; ipk.forEach(function(r) {
                                tbody.appendChild(tr([formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk), safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.durasi_jam), safeStr(r.cctv_terekam), safeStr(r.kategori_ijk, 35), safeStr(r.status_pekerjaan)]));
                            }); }
                        }
                        if (okk.length === 0) { document.getElementById('intervensiOkkEmpty').classList.remove('d-none'); document.getElementById('intervensiOkkTableWrap').classList.add('d-none'); } else {
                            document.getElementById('intervensiOkkEmpty').classList.add('d-none');
                            document.getElementById('intervensiOkkTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableOkk tbody');
                            if (tbody) { tbody.innerHTML = ''; okk.forEach(function(r) {
                                tbody.appendChild(tr([formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk), safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.jenis_ijk, 35), safeStr(r.layer_pengawas)]));
                            }); }
                        }
                        if (oak.length === 0) { document.getElementById('intervensiOakEmpty').classList.remove('d-none'); document.getElementById('intervensiOakTableWrap').classList.add('d-none'); } else {
                            document.getElementById('intervensiOakEmpty').classList.add('d-none');
                            document.getElementById('intervensiOakTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableOak tbody');
                            if (tbody) { tbody.innerHTML = ''; oak.forEach(function(r) {
                                tbody.appendChild(tr([safeStr(r.activity), safeStr(r.sub_activity), safeStr(r.submit_date), safeStr(r.submit_by), safeStr(r.kode_sid_pelapor), safeStr(r.kode_sid_team), safeStr(r.conclusion, 50), safeStr(r.site)]));
                            }); }
                        }
                    })
                    .catch(function(err) {
                        document.getElementById('intervensiIpkLoading').classList.add('d-none');
                        document.getElementById('intervensiOkkLoading').classList.add('d-none');
                        document.getElementById('intervensiOakLoading').classList.add('d-none');
                        document.getElementById('intervensiIpkEmpty').classList.remove('d-none');
                        document.getElementById('intervensiOkkEmpty').classList.remove('d-none');
                        document.getElementById('intervensiOakEmpty').classList.remove('d-none');
                        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal memuat', text: err.message || 'Gagal memuat data.' });
                    });
            }

            function doLayer1Fetch() {
                if (!hasLayer1) return;
                var layer1UsersEl2 = document.getElementById('intervensiLayer1Users');
                var layer1EmptyEl2 = document.getElementById('intervensiLayer1Empty');
                var layer1LoadingEl2 = document.getElementById('intervensiLayer1Loading');
                var okkLayer1UsersEl2 = document.getElementById('intervensiOkkLayer1Users');
                var okkLayer1EmptyEl2 = document.getElementById('intervensiOkkLayer1Empty');
                var okkLayer1LoadingEl2 = document.getElementById('intervensiOkkLayer1Loading');
                layer1LoadingEl2.classList.remove('d-none');
                layer1UsersEl2.innerHTML = '';
                layer1EmptyEl2.classList.add('d-none');
                if (okkLayer1LoadingEl2) { okkLayer1LoadingEl2.classList.remove('d-none'); okkLayer1UsersEl2.innerHTML = ''; okkLayer1EmptyEl2.classList.add('d-none'); }
                var qs = new URLSearchParams();
                if (sidLayer1) qs.set('sid_layer_1', sidLayer1);
                if (namaLayer1) qs.set('nama_layer_1', namaLayer1);
                fetch(layer1UsersApiUrl + '?' + qs.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        layer1LoadingEl2.classList.add('d-none');
                        if (okkLayer1LoadingEl2) okkLayer1LoadingEl2.classList.add('d-none');
                        var users = (res && res.success && res.users) ? res.users : [];
                        var displayName = (res && res.nama_layer_1) ? res.nama_layer_1 : namaLayer1;
                        document.getElementById('intervensiLayer1NameDisplay').textContent = displayName || '—';
                        if (document.getElementById('intervensiOkkLayer1NameDisplay')) document.getElementById('intervensiOkkLayer1NameDisplay').textContent = displayName || '—';
                        var ipkMsg = (displayName || 'PIC') + ', anda harus mengisi INSPEKSI PRA KERJA (IPK)\n' + ipkFormLink;
                        var okkMsg = (displayName || 'PIC') + ', mohon perhatian untuk OBSERVASI KEGIATAN KERJA (OKK) sesuai IKK ini.';
                        users.forEach(function(u) {
                            var num = normalizeWaNumber(u.selular);
                            var label = u.nama || u.username || 'User';
                            if (num) {
                                var aIpk = document.createElement('a');
                                aIpk.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(ipkMsg);
                                aIpk.target = '_blank';
                                aIpk.rel = 'noopener';
                                aIpk.className = 'btn btn-sm btn-success';
                                aIpk.innerHTML = '<i class="material-icons-outlined me-1" style="font-size:16px;">send</i> Intervensi by WA — ' + label;
                                layer1UsersEl2.appendChild(aIpk);
                            }
                            if (okkLayer1UsersEl2 && num) {
                                var aOkk = document.createElement('a');
                                aOkk.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(okkMsg);
                                aOkk.target = '_blank';
                                aOkk.rel = 'noopener';
                                aOkk.className = 'btn btn-sm btn-success';
                                aOkk.innerHTML = '<i class="material-icons-outlined me-1" style="font-size:16px;">send</i> Intervensi by WA — ' + label;
                                okkLayer1UsersEl2.appendChild(aOkk);
                            }
                        });
                        if (users.length === 0) { layer1EmptyEl2.classList.remove('d-none'); if (okkLayer1EmptyEl2) okkLayer1EmptyEl2.classList.remove('d-none'); }
                    })
                    .catch(function() {
                        layer1LoadingEl2.classList.add('d-none');
                        layer1EmptyEl2.classList.remove('d-none');
                        if (okkLayer1LoadingEl2) { okkLayer1LoadingEl2.classList.add('d-none'); if (okkLayer1EmptyEl2) okkLayer1EmptyEl2.classList.remove('d-none'); }
                    });
            }

            function doOakLayers234Fetch() {
                var qs = new URLSearchParams();
                qs.set('sid_layer_2', data.sid_layer_2 || '');
                qs.set('sid_layer_3', data.sid_layer_3 || '');
                qs.set('sid_layer_4', data.sid_layer_4 || '');
                qs.set('nama_layer_2', data.nama_layer_2 || '');
                qs.set('nama_layer_3', data.nama_layer_3 || '');
                qs.set('nama_layer_4', data.nama_layer_4 || '');
                fetch(layers234UsersApiUrl + '?' + qs.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        [2, 3, 4].forEach(function(n) {
                            var key = 'layer_' + n;
                            var usersEl = document.getElementById('intervensiOakLayer' + n + 'Users');
                            var emptyEl = document.getElementById('intervensiOakLayer' + n + 'Empty');
                            var loadingEl = document.getElementById('intervensiOakLayer' + n + 'Loading');
                            var nameEl = document.getElementById('intervensiOakLayer' + n + 'Name');
                            if (loadingEl) loadingEl.classList.add('d-none');
                            var layerData = res && res[key] ? res[key] : { users: [], nama_layer: '' };
                            var users = layerData.users || [];
                            var displayName = layerData.nama_layer || '—';
                            if (nameEl) nameEl.textContent = displayName;
                            if (!usersEl) return;
                            usersEl.innerHTML = '';
                            var oakMsg = (displayName !== '—' ? displayName : 'PIC') + ', mohon perhatian untuk OAK (Observasi Aktivitas Kerja) sesuai IKK ini.';
                            users.forEach(function(u) {
                                var num = normalizeWaNumber(u.selular);
                                if (!num) return;
                                var label = u.nama || u.username || 'User';
                                var a = document.createElement('a');
                                a.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(oakMsg);
                                a.target = '_blank';
                                a.rel = 'noopener';
                                a.className = 'btn btn-sm btn-warning text-dark';
                                a.innerHTML = '<i class="material-icons-outlined me-1" style="font-size:14px;">send</i> Intervensi by WA';
                                a.title = label;
                                usersEl.appendChild(a);
                            });
                            if (users.length === 0 && emptyEl) emptyEl.classList.remove('d-none');
                        });
                    })
                    .catch(function() {
                        [2, 3, 4].forEach(function(n) {
                            var loadingEl = document.getElementById('intervensiOakLayer' + n + 'Loading');
                            var emptyEl = document.getElementById('intervensiOakLayer' + n + 'Empty');
                            if (loadingEl) loadingEl.classList.add('d-none');
                            if (emptyEl) emptyEl.classList.remove('d-none');
                        });
                    });
            }

            doIntervensiFetch();
            doLayer1Fetch();
            doOakLayers234Fetch();
            return;
        }

        var btn = e.target.closest('.btn-detail-dopm') || (e.target.closest('.dopm-matriks-row') && !e.target.closest('.btn-intervensi-dopm') ? e.target.closest('.dopm-matriks-row') : null);
        if (!btn) return;
        var data = JSON.parse(btn.getAttribute('data-dopm') || '{}');
        window._lastDetailDopmData = data;
        var modalDoc = document.getElementById('detailDopmModal');
        document.getElementById('detailDopmTitle').textContent = (data.id_dop || 'Detail') + ' — ' + (data.nama_pekerjaan || 'DOPM').substring(0, 50);
        document.getElementById('detailDopmSubtitle').textContent = 'Kode IKK: ' + (data.kode_ikk || '—');
        function dash(val) { return (val == null || val === '' || val === undefined) ? '—' : String(val); }
        document.getElementById('detailDopmId').textContent = dash(data.id_dop);
        document.getElementById('detailDopmKodeIkk').textContent = dash(data.kode_ikk);
        document.getElementById('detailDopmNamaPekerjaan').textContent = dash(data.nama_pekerjaan);
        document.getElementById('detailDopmSite').textContent = dash(data.site_ijin_kerja_khusus);
        document.getElementById('detailDopmPerusahaan').textContent = dash(data.perusahaan_ijin_kerja_khusus);
        document.getElementById('detailDopmJenisIjk').textContent = dash(data.jenis_ijin_kerja_khusus);
        document.getElementById('detailDopmTanggal').textContent = dash(data.tanggal_dop);
        document.getElementById('detailDopmStatus').textContent = dash(data.status);
        document.getElementById('detailDopmLayer1').textContent = dash(data.nama_layer_1);
        document.getElementById('detailDopmLayer2').textContent = dash(data.nama_layer_2);
        document.getElementById('detailDopmLayer3').textContent = dash(data.nama_layer_3);
        document.getElementById('detailDopmLayer4').textContent = dash(data.nama_layer_4);
        document.getElementById('badgeIpkIkk').textContent = '0';
        document.getElementById('badgeOkk').textContent = '0';
        document.getElementById('badgeOak').textContent = '0';
        document.getElementById('statCountIpkIkk').textContent = '0';
        document.getElementById('statCountOkk').textContent = '0';
        document.getElementById('statCountOak').textContent = '0';
        showLoading('ipkIkk');
        showLoading('okk');
        showLoading('oak');
        document.getElementById('oakContext').classList.add('d-none');
        modal.show();
        var params = new URLSearchParams({
            kode_ikk: data.kode_ikk || '',
            jenis_ijin_kerja_khusus: data.jenis_ijin_kerja_khusus || '',
            sid_layer_2: data.sid_layer_2 || '',
            sid_layer_3: data.sid_layer_3 || '',
            sid_layer_4: data.sid_layer_4 || '',
            nama_layer_2: data.nama_layer_2 || '',
            nama_layer_3: data.nama_layer_3 || '',
            nama_layer_4: data.nama_layer_4 || ''
        });
        function doFetch() {
        fetch(modalApiUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                var ct = (r.headers.get('Content-Type') || '').toLowerCase();
                if (ct.indexOf('application/json') === -1) throw new Error('Response bukan JSON');
                return r.json();
            })
            .then(function(res) {
                if (!res || !res.success) throw new Error(res && res.message ? res.message : 'Request failed');
                var ipk = res.ipk_ikk || [];
                var okk = res.okk || [];
                var oak = res.oak || [];
                var ctx = res.dopm_context || {};
                var layerNames = [ctx.nama_layer_2, ctx.nama_layer_3, ctx.nama_layer_4].filter(Boolean).join(' / ') || '—';
                document.getElementById('oakLayerNames').textContent = layerNames;
                document.getElementById('badgeIpkIkk').textContent = ipk.length;
                document.getElementById('badgeOkk').textContent = okk.length;
                document.getElementById('badgeOak').textContent = oak.length;
                document.getElementById('statCountIpkIkk').textContent = ipk.length;
                document.getElementById('statCountOkk').textContent = okk.length;
                document.getElementById('statCountOak').textContent = oak.length;
                if (layerNames !== '—') document.getElementById('oakContext').classList.remove('d-none');

                // Tabel IPK-IKK (pastikan ambil elemen di dalam modal)
                var tbodyIpk = modalDoc.querySelector('#tableIpkIkk tbody');
                if (tbodyIpk) {
                    tbodyIpk.innerHTML = '';
                    if (ipk.length === 0) {
                        showEmpty('ipkIkk');
                    } else {
                        showTable('ipkIkk');
                        ipk.forEach(function(r) {
                            tbodyIpk.appendChild(tr([
                                formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk),
                                safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.durasi_jam),
                                safeStr(r.cctv_terekam), safeStr(r.kategori_ijk, 35), safeStr(r.status_pekerjaan)
                            ]));
                        });
                    }
                }

                // Tabel OKK
                var tbodyOkk = modalDoc.querySelector('#tableOkk tbody');
                if (tbodyOkk) {
                    tbodyOkk.innerHTML = '';
                    if (okk.length === 0) {
                        showEmpty('okk');
                    } else {
                        showTable('okk');
                        okk.forEach(function(r) {
                            tbodyOkk.appendChild(tr([
                                formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk),
                                safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.jenis_ijk, 35), safeStr(r.layer_pengawas)
                            ]));
                        });
                    }
                }

                // Tabel OAK
                var tbodyOak = modalDoc.querySelector('#tableOak tbody');
                if (tbodyOak) {
                    tbodyOak.innerHTML = '';
                    if (oak.length === 0) {
                        showEmpty('oak');
                    } else {
                        showTable('oak');
                        oak.forEach(function(r) {
                            tbodyOak.appendChild(tr([
                                safeStr(r.activity), safeStr(r.sub_activity), safeStr(r.submit_date), safeStr(r.submit_by),
                                safeStr(r.kode_sid_pelapor), safeStr(r.kode_sid_team), safeStr(r.conclusion, 50), safeStr(r.site)
                            ]));
                        });
                    }
                }
            })
            .catch(function(err) {
                showEmpty('ipkIkk');
                showEmpty('okk');
                showEmpty('oak');
                console.error(err);
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal memuat', text: err.message || 'Gagal memuat data detail.' });
            });
        }
        modalEl.addEventListener('shown.bs.modal', function onShown() {
            modalEl.removeEventListener('shown.bs.modal', onShown);
            doFetch();
        }, { once: true });
    });

    // Tombol Intervensi (footer + di dalam modal): tutup detail, buka modal Intervensi dengan data yang sama
    function openIntervensiFromDetail() {
        var data = window._lastDetailDopmData;
        if (!data || !intervensiModal) return;
        if (modal && modal.hide) modal.hide();
        var fake = document.createElement('button');
        fake.className = 'btn-intervensi-dopm d-none';
        fake.setAttribute('data-dopm', JSON.stringify(data));
        document.body.appendChild(fake);
        fake.click();
        fake.remove();
    }
    [document.getElementById('btnIntervensiFromDetail'), document.getElementById('btnIntervensiFromDetailTop')].forEach(function(btn) {
        if (btn && intervensiModal) btn.addEventListener('click', openIntervensiFromDetail);
    });

    // Klik Enter pada baris matriks (Need Action / Warning / Complete) buka detail
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        var row = e.target.closest('.dopm-matriks-row');
        if (!row || e.target.closest('.btn-intervensi-dopm')) return;
        e.preventDefault();
        row.click();
    });
})();
</script>
@endsection



