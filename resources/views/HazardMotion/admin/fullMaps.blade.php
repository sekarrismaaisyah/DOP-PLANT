{{-- @extends('layouts.masterMotionHazardAdmin') --}}
@extends('layouts.master-home')

@section('title', 'Hazard Detection - Beraucoal')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Select2 CSS for PIC dropdown -->
<link href="{{ URL::asset('build/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('build/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
<style>
    /* ============================================
   GOOGLE MAPS STYLE INTERFACE (FULL REWORK)
   ============================================ */

/* Global top height (header + chips) */
:root{
  --gm-top-h: 112px; /* 64 header + ~48 chips */
}

/* Full-screen container - full maps */
.map-fullscreen-container{
  position: fixed;
  inset: 0;
  z-index: 999;
  background: #f9fafb;
  overflow: hidden;
  padding-top: 0;
  transition: padding-left .25s ease;
}

.map-fullscreen-container.gm-left-sidebar-open{
  padding-left: 240px;
}

/* When sidebar is collapsed, ensure no padding */
.map-fullscreen-container:not(.gm-left-sidebar-open){
  padding-left: 0 !important;
}

/* Map size - full screen */
#hazardMap{
  width: 100%;
  height: 100vh;
  border: 0;
  outline: 0;
  background: #f1f3f4;
}

/* -------------------
   Header (Google Maps)
   ------------------- */
.gm-header{
  position: absolute;
  top: 0; left: 0; right: 0;
  z-index: 1002;
  background: transparent;
  border: 0;
  pointer-events: none;
  transition: left .25s ease;
}

/* Adjust header position when sidebar is open */
.map-fullscreen-container.gm-left-sidebar-open .gm-header{
  left: 240px;
}

.gm-header > *,
.gm-header * {
  pointer-events: auto;
}

/* top row */
.gm-header-top{
  height: 90px;
  padding: 14px 24px;
  display: flex;
  align-items: center;
  gap: 14px;
}

/* menu */
.gm-menu-btn{
  width: 52px;
  height: 52px;
  border: none;
  background: transparent;
  border-radius: 50%;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  transition: background .15s ease;
}
.gm-menu-btn:hover{ background: rgba(60,64,67,.08); }
.gm-menu-btn i{ font-size: 30px; color:#5f6368; }

/* search box container */
.gm-search-container{
  flex: 0 0 auto;
  width: 650px;
  max-width: 650px;
  position: relative;
  margin-left: 6px;
}

/* search input */
.gm-search-box{
  width: 100%;
  height: 60px;
  border: none;
  border-radius: 30px;
  padding: 0 70px 0 70px;
  font-size: 19px;
  background: #fff;
  box-shadow: 0 2px 6px rgba(60,64,67,.16);
  transition: box-shadow .15s ease;
  font-family: Roboto, Arial, sans-serif;
  color: #202124;
}
.gm-search-box:hover{ box-shadow: 0 3px 10px rgba(60,64,67,.24); }
.gm-search-box:focus{
  outline: none;
  box-shadow: 0 3px 10px rgba(60,64,67,.24);
}
.gm-search-icon{
  position: absolute;
  left: 24px;
  top: 50%;
  transform: translateY(-50%);
  color: #9aa0a6;
  font-size: 26px;
  pointer-events: none;
}

/* directions icon */
.gm-directions-btn{
  position:absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 52px;
  height: 52px;
  border: none;
  background: transparent;
  border-radius: 50%;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  transition: background .15s ease;
}
.gm-directions-btn:hover{ background: rgba(60,64,67,.08); }
.gm-directions-btn i{ font-size: 26px; color: #1a73e8; }

/* right header icons */
.gm-header-right{
  display:flex;
  align-items:center;
  gap: 10px;
  margin-left: auto;
}

.gm-header-icon-btn{
  width:52px;
  height:52px;
  border:none;
  background:transparent;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  transition: background .15s ease;
  position: relative;
}
.gm-header-icon-btn:hover{ background: rgba(60,64,67,.08); }
.gm-header-icon-btn i{ font-size: 30px; color:#5f6368; }

/* Notification Bell Icon with Blinking Animation */
.gm-notification-btn {
  position: relative;
  overflow: visible;
}

.gm-notification-btn i {
  animation: bellRing 2s ease-in-out infinite;
  transform-origin: center top;
  position: relative;
  z-index: 2;
}

@keyframes bellRing {
  0%, 100% {
    transform: rotate(0deg);
  }
  10%, 30% {
    transform: rotate(-10deg);
  }
  20%, 40% {
    transform: rotate(10deg);
  }
  50% {
    transform: rotate(0deg);
  }
}

/* Notification Badge */
.notification-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 8px;
  height: 8px;
  background: #ea4335;
  border-radius: 50%;
  border: 2px solid #ffffff;
  animation: pulse 2s ease-in-out infinite;
  z-index: 3;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.2);
  }
}

/* Notification Pulse Effect */
.notification-pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: rgba(234, 67, 53, 0.6);
  animation: ripple 2s ease-out infinite;
  z-index: 1;
}

@keyframes ripple {
  0% {
    transform: translate(-50%, -50%) scale(0.8);
    opacity: 1;
    background: rgba(234, 67, 53, 0.7);
  }
  50% {
    background: rgba(234, 67, 53, 0.5);
  }
  100% {
    transform: translate(-50%, -50%) scale(1.5);
    opacity: 0;
    background: rgba(234, 67, 53, 0.2);
  }
}

/* Blinking Effect */
.gm-notification-btn::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: rgba(234, 67, 53, 0.2);
  animation: blink 2s ease-in-out infinite;
  z-index: 0;
}

@keyframes blink {
  0%, 100% {
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.8);
  }
  50% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
}

/* Notification Panel */
.gm-notification-panel {
  position: absolute;
  top: 70px;
  right: 0;
  width: 400px;
  max-height: 600px;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(60, 64, 67, 0.3);
  z-index: 1003;
  display: none;
  overflow: hidden;
  font-family: Roboto, Arial, sans-serif;
}

.gm-notification-panel.active {
  display: block;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.gm-notification-panel-header {
  padding: 16px 20px;
  border-bottom: 1px solid #e8eaed;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f8f9fa;
}

.gm-notification-panel-title {
  font-size: 18px;
  font-weight: 500;
  color: #202124;
  margin: 0;
}

.gm-notification-panel-pin {
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #5f6368;
  transition: all 0.15s ease;
}

.gm-notification-panel-pin:hover {
  background: rgba(60, 64, 67, 0.08);
}

.gm-notification-panel-pin.pinned {
  color: #ea4335;
  transform: rotate(45deg);
}

.gm-notification-panel-pin.pinned i {
  color: #ea4335;
}

.gm-notification-panel-close {
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #5f6368;
  transition: background 0.15s ease;
}

.gm-notification-panel-close:hover {
  background: rgba(60, 64, 67, 0.08);
}

.gm-notification-panel-body {
  max-height: 520px;
  overflow-y: auto;
  padding: 8px 0;
}

.gm-notification-category {
  padding: 12px 20px;
  border-bottom: 1px solid #f1f3f4;
  cursor: pointer;
  transition: background 0.15s ease;
}

.gm-notification-category:hover {
  background: #f8f9fa;
}

.gm-notification-category-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.gm-notification-category-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 16px;
  font-weight: 500;
  color: #202124;
  flex: 1;
}

.gm-notification-color-indicator {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2px solid #ffffff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.gm-notification-color-indicator.red {
  background: #d92d20;
}

.gm-notification-color-indicator.orange {
  background: #f79009;
}

.gm-notification-color-indicator.green {
  background: #12b76a;
}

.gm-notification-category-count {
  font-size: 14px;
  font-weight: 500;
  color: #5f6368;
  background: #f1f3f4;
  padding: 4px 12px;
  border-radius: 12px;
}

.gm-notification-location-list {
  max-height: 200px;
  overflow-y: auto;
  margin-top: 8px;
  padding-left: 28px;
}

.gm-notification-location-item {
  padding: 8px 12px;
  font-size: 14px;
  color: #3c4043;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.gm-notification-location-item:hover {
  background: #e8eaed;
}

.gm-notification-location-item i {
  font-size: 16px;
  color: #5f6368;
}

.gm-notification-empty {
  padding: 40px 20px;
  text-align: center;
  color: #9aa0a6;
  font-size: 14px;
}

.gm-notification-category.expanded .gm-notification-location-list {
  display: block;
}

.gm-notification-category:not(.expanded) .gm-notification-location-list {
  display: none;
}

.gm-notification-category-arrow {
  transition: transform 0.2s ease;
  color: #5f6368;
}

.gm-notification-category.expanded .gm-notification-category-arrow {
  transform: rotate(90deg);
}

.gm-profile-btn{
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #1a73e8;
  color: #fff;
  border:none;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:500;
  font-size:17px;
  cursor:pointer;
  transition: box-shadow .15s ease;
}
.gm-profile-btn:hover{ box-shadow: 0 2px 6px rgba(0,0,0,.25); }

/* chips row - sejajar dengan search box */
.gm-category-filters{
  display:flex;
  align-items:center;
  gap: 12px;
  padding: 0;
  margin: 0 12px;
  overflow-x:auto;
  scrollbar-width:none;
  flex: 1;
  min-width: 0;
  max-width: none;
}
.gm-category-filters::-webkit-scrollbar{ display:none; }

.gm-category-item{
  display:flex;
  align-items:center;
  gap: 12px;
  padding: 12px 20px;
  background: #fff;
  border: 1px solid #dadce0;
  border-radius: 999px;
  font-size: 17px;
  color:#3c4043;
  white-space:nowrap;
  cursor:pointer;
  text-decoration:none;
  box-shadow: 0 1px 2px rgba(60,64,67,.16);
  transition: background .15s ease, box-shadow .15s ease;
  font-family: Roboto, Arial, sans-serif;
}
.gm-category-item:hover{
  background:#f8f9fa;
  box-shadow: 0 1px 3px rgba(60,64,67,.24);
}
.gm-category-item.active{
  background:#e8f0fe;
  border-color: #1a73e8;
  color: #1a73e8;
  box-shadow: 0 2px 4px rgba(26,115,232,.2);
}
.gm-category-item.active i{
  color: #1a73e8;
}
.gm-category-item i{
  font-size: 22px;
  color: #5f6368;
}

/* -------------------
   Left Sidebar (GMaps)
   ------------------- */
.gm-left-sidebar{
  position: fixed;
  left: 0;
  top: 0;
  width: 240px;
  height: 100vh;
  background: #fff;
  z-index: 1003;
  overflow-y: auto;
  overflow-x: hidden;
  transition: transform .25s ease, opacity .25s ease, visibility .25s ease;
  box-shadow: 2px 0 8px rgba(0,0,0,0.1);
  border: none;
  display:flex;
  flex-direction:column;
}

/* Sidebar backdrop overlay for mobile */
.gm-sidebar-backdrop{
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 1002;
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
  transition: opacity .25s ease, visibility .25s ease;
  display: none; /* Hidden by default on desktop */
}

.map-fullscreen-container.gm-left-sidebar-open .gm-sidebar-backdrop{
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}
.gm-left-sidebar.collapsed{ 
  transform: translateX(-100%);
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
  width: 0 !important;
  overflow: hidden;
  background: transparent !important;
}

/* Hide all content when sidebar is collapsed */
.gm-left-sidebar.collapsed * {
  display: none !important;
}

.gm-sidebar-menu-btn{
  width:40px;
  height:40px;
  margin: 8px 8px 8px 12px;
  border:none;
  background:transparent;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  transition: background .15s ease;
}
.gm-sidebar-menu-btn:hover{ background: rgba(60,64,67,.08); }
.gm-sidebar-menu-btn i{ font-size:24px; color:#5f6368; }

.gm-sidebar-nav{ padding: 8px 0; }

.gm-sidebar-nav-item{
  display:flex;
  align-items:center;
  gap: 16px;
  padding: 12px 16px 12px 20px;
  cursor:pointer;
  text-decoration:none;
  color:#202124;
  font-size:14px;
  font-family: Roboto, Arial, sans-serif;
}
.gm-sidebar-nav-item:hover{ background:#f1f3f4; }
.gm-sidebar-nav-item i{ font-size:20px; color:#5f6368; }

.gm-sidebar-section{
  padding: 12px 0;
  flex:1;
  overflow-y:auto;
}
.gm-sidebar-section-title{
  font-size:12px;
  font-weight:500;
  color:#5f6368;
  text-transform:uppercase;
  letter-spacing:.5px;
  padding: 0 20px;
  margin: 6px 0 10px;
  font-family: Roboto, Arial, sans-serif;
}

.gm-recent-item{
  display:flex;
  align-items:center;
  gap: 12px;
  padding: 12px 16px 12px 20px;
  cursor:pointer;
  position: relative;
}
.gm-recent-item:hover{ background:#f1f3f4; }
.gm-recent-item::before{
  content:"";
  position:absolute;
  left:0; top:0; bottom:0;
  width:3px;
  background: transparent;
}
.gm-recent-item:hover::before{ background:#1a73e8; }

.gm-recent-icon{
  width:48px;
  height:48px;
  border-radius:8px;
  background:#f1f3f4;
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;
  overflow:hidden;
  flex-shrink:0;
}
.gm-recent-icon i{ font-size:20px; color:#5f6368; }

.gm-recent-badge{
  position:absolute;
  top:-4px;
  right:-4px;
  width:20px;
  height:20px;
  border-radius:50%;
  background:#ea4335;
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:11px;
  font-weight:500;
  border:2px solid #fff;
}

.gm-recent-title{
  font-size:14px;
  color:#202124;
  font-weight:400;
  font-family: Roboto, Arial, sans-serif;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
.gm-recent-subtitle{
  font-size:12px;
  color:#5f6368;
  font-family: Roboto, Arial, sans-serif;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}

.gm-view-more{
  margin: 8px 12px;
  padding: 10px 12px;
  border-radius:999px;
  color:#1a73e8;
  text-decoration:none;
  display:flex;
  align-items:center;
  justify-content:space-between;
  font-family: Roboto, Arial, sans-serif;
}
.gm-view-more:hover{ background:#f1f3f4; }

.gm-get-app{
  position: sticky;
  bottom: 0;
  padding: 14px 12px;
  background:#fff;
}
.gm-get-app-btn{
  display:flex;
  align-items:center;
  gap: 12px;
  padding: 10px 14px;
  border-radius:999px;
  background:#f8f9fa;
  text-decoration:none;
  color:#202124;
  font-family: Roboto, Arial, sans-serif;
  box-shadow: 0 1px 2px rgba(60,64,67,.16);
}
.gm-get-app-btn:hover{ background:#e8eaed; }
.gm-get-app-btn i{ color:#5f6368; }

/* layers button in sidebar */
.gm-layer-toggle{
  margin: 0 12px 12px;
  width: calc(100% - 24px);
  height: 40px;
  border-radius: 2px;
  border: none;
  background: #fff;
  box-shadow: 0 1px 2px rgba(60,64,67,.16);
  cursor:pointer;
}
.gm-layer-toggle:hover{ background:#f8f9fa; }

/* -------------------
   Right Controls (GMaps)
   ------------------- */
.gm-map-controls{
  position: fixed;
  right: 16px;
  top: 16px;
  z-index: 1000;
  display:flex;
  flex-direction:column;
  align-items:flex-end;
}

.gm-control-btn{
  width:40px;
  height:40px;
  background:#fff;
  border:none;
  border-radius:2px;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  box-shadow: 0 1px 4px rgba(0,0,0,.3);
  transition: background .15s ease;
  position: relative;
}

.gm-control-btn:hover{ background:#f8f9fa; }

.gm-control-btn:not(:last-child):not(.compass)::after{
  content:"";
  position:absolute;
  left:0; right:0; bottom:0;
  height:1px;
  background: rgba(218,220,224,.85);
}

/* compass */
.gm-control-btn.compass{
  width:44px;
  height:44px;
  border-radius:50%;
  background:#000;
  margin-bottom: 8px;
}
.gm-control-btn.compass i{ color:#fff; font-size: 20px; }

/* streetview and layers spacing */
.gm-control-btn.street-view{ margin-top: 8px; }
.gm-control-btn.street-view i{ color:#fbbc04; }

/* copyright */
.gm-copyright{
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: rgba(255,255,255,.65);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  font-size: 10px;
  color:#5f6368;
  text-align:center;
  padding: 6px 12px;
  pointer-events:none;
  font-family: Roboto, Arial, sans-serif;
}
.map-fullscreen-container.gm-left-sidebar-open .gm-copyright{ left: 240px; }
.gm-copyright a{
  color:#1a73e8;
  text-decoration:none;
  pointer-events:auto;
}
.gm-copyright a:hover{ text-decoration:underline; }

/* -------------------
   Map Layer Filter (Bottom Left) - New Design
   ------------------- */
.gm-layer-wrap {
  position: absolute;
  bottom: 16px;
  left: 16px;
  z-index: 10002;
}

/* Compact button (Layers) */
.gm-layer-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 16px;
  background: rgba(255, 255, 255, .92);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(0, 0, 0, .10);
  box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
  cursor: pointer;
  user-select: none;
  transition: box-shadow .15s ease, transform .12s ease, border-color .15s ease;
}

.gm-layer-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, .14);
}

.gm-layer-btn:active {
  transform: translateY(0px);
}

.gm-layer-btn .layer-text {
  font-weight: 800;
  color: #111827;
  letter-spacing: .2px;
}

/* Better icon */
.gm-layer-icon {
  width: 24px;
  height: 24px;
  stroke: currentColor;
  fill: none;
  stroke-width: 2.2;
  stroke-linecap: round;
  stroke-linejoin: round;
  color: #111827;
  opacity: .92;
}

.gm-layer-btn[aria-expanded="true"] .gm-layer-icon {
  color: #0d6efd;
  opacity: 1;
}

/* Dropdown panel */
.gm-layer-panel {
  margin-top: 10px;
  background: rgba(255, 255, 255, .92);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(0, 0, 0, .08);
  border-radius: 18px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
  padding: 10px;
  max-width: min(980px, calc(100vw - 24px));
}

.gm-layer-scroll {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding: 2px;
  scrollbar-width: none;
}

.gm-layer-scroll::-webkit-scrollbar {
  display: none;
}

/* Tile toggle */
.gm-tile {
  width: 128px;
  min-width: 128px;
  border-radius: 14px;
  border: 2px solid transparent;
  padding: 8px;
  cursor: pointer;
  user-select: none;
  background: #fff;
  transition: transform .12s ease, border-color .12s ease, box-shadow .12s ease;
}

.gm-tile:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, .10);
}

.gm-thumb {
  height: 56px;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, .10);
  background-size: cover;
  background-position: center;
}

.gm-label {
  margin-top: 8px;
  font-size: .92rem;
  font-weight: 800;
  line-height: 1.15;
  color: #1f2937;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.gm-sub {
  font-size: .78rem;
  color: #6b7280;
  margin-top: 2px;
}

/* Active state */
.btn-check:checked + .gm-tile {
  border-color: #0d6efd;
  box-shadow: 0 10px 24px rgba(13, 110, 253, .18);
}

.btn-check:focus + .gm-tile {
  box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .20);
}

/* Old layer option styles removed - using new tile design */
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect fill="%234caf50" width="100" height="100"/><path fill="%2381c784" d="M20 40 L80 40 L70 60 L30 60 Z"/><circle fill="%2364b5f6" cx="75" cy="25" r="8"/><circle fill="%2364b5f6" cx="25" cy="30" r="5"/></svg>') center/cover;
  opacity: 0.9;
}

.gm-layer-option.satellite .gm-layer-option-icon::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 75% 20%, rgba(100, 181, 246, 0.4) 0%, transparent 50%);
}

.gm-layer-option.terrain .gm-layer-option-icon {
  background: linear-gradient(135deg, #e0e0e0 0%, #bdbdbd 30%, #9e9e9e 60%, #757575 100%);
  position: relative;
}

.gm-layer-option.terrain .gm-layer-option-icon::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  background-image: 
    repeating-linear-gradient(
      0deg,
      rgba(255,255,255,.2) 0px,
      rgba(255,255,255,.2) 2px,
      transparent 2px,
      transparent 8px
    ),
    repeating-linear-gradient(
      90deg,
      rgba(0,0,0,.12) 0px,
      rgba(0,0,0,.12) 1px,
      transparent 1px,
      transparent 6px
    );
  border-radius: 8px;
}

.gm-layer-option.terrain .gm-layer-option-icon::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 30%;
  background: linear-gradient(to top, rgba(117, 117, 117, 0.3) 0%, transparent 100%);
  border-radius: 0 0 8px 8px;
}

.gm-layer-option.traffic .gm-layer-option-icon {
  background: linear-gradient(135deg, #ffffff 0%, #f5f5f5 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.gm-layer-option.traffic .gm-layer-option-icon::before {
  content: '';
  position: absolute;
  width: 65%;
  height: 65%;
  background: 
    linear-gradient(90deg, #4caf50 0%, #4caf50 33.33%, #ffeb3b 33.33%, #ffeb3b 66.66%, #f44336 66.66%, #f44336 100%);
  border-radius: 4px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.gm-layer-option.traffic .gm-layer-option-icon::after {
  content: '';
  position: absolute;
  width: 70%;
  height: 4px;
  background: rgba(0,0,0,0.1);
  border-radius: 2px;
  bottom: 15%;
  box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.gm-layer-option.transit .gm-layer-option-icon {
  background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.gm-layer-option.transit .gm-layer-option-icon::before {
  content: 'M';
  position: absolute;
  font-size: 24px;
  font-weight: 700;
  color: #1976d2;
  line-height: 1;
  top: 20%;
  left: 50%;
  transform: translateX(-50%);
  z-index: 2;
  font-family: 'Roboto', Arial, sans-serif;
  text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.gm-layer-option.transit .gm-layer-option-icon::after {
  content: '';
  position: absolute;
  width: 85%;
  height: 4px;
  background: linear-gradient(90deg, #1976d2 0%, #42a5f5 100%);
  top: 35%;
  left: 7.5%;
  z-index: 1;
  border-radius: 2px;
  box-shadow: 0 1px 3px rgba(25, 118, 210, 0.3);
}

.gm-layer-option.biking .gm-layer-option-icon {
  background: linear-gradient(135deg, #ffffff 0%, #e8f5e9 100%);
  position: relative;
  overflow: hidden;
}

.gm-layer-option.biking .gm-layer-option-icon::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  background: 
    radial-gradient(circle at 50% 50%, #4caf50 0%, #4caf50 13%, transparent 13%),
    radial-gradient(circle at 50% 50%, transparent 13%, #4caf50 13%, #4caf50 26%, transparent 26%);
  border-radius: 8px;
}

.gm-layer-option.biking .gm-layer-option-icon::after {
  content: '';
  position: absolute;
  width: 65%;
  height: 4px;
  background: linear-gradient(90deg, #4caf50 0%, #66bb6a 100%);
  top: 50%;
  left: 17.5%;
  border-radius: 2px;
  box-shadow: 0 1px 3px rgba(76, 175, 80, 0.3);
  transform: translateY(-50%);
}

.gm-layer-option.more .gm-layer-option-icon {
  background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.gm-layer-option.more .gm-layer-option-icon::before {
  content: '';
  width: 24px;
  height: 24px;
  background: linear-gradient(135deg, #424242 0%, #212121 100%);
  clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%);
  transform: rotate(45deg);
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.gm-layer-option-label {
  font-size: 11px;
  color: #616161;
  font-weight: 500;
  text-align: center;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
  transition: all 0.3s ease;
  letter-spacing: 0.2px;
  position: relative;
  z-index: 1;
}

.gm-layer-option:hover .gm-layer-option-label {
  color: #424242;
}

.gm-layer-option.active .gm-layer-option-label {
  color: #1976d2;
  font-weight: 600;
}

/* Popup style untuk OpenLayers - CCTV, Insiden, Area Kerja */
.ol-popup {
  position: absolute;
  background-color: #ffffff !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  padding: 15px;
  border-radius: 10px;
  border: 1px solid #cccccc;
  bottom: 12px;
  left: -50px;
  min-width: 200px;
  z-index: 10000;
}

#popup-content {
  background-color: #ffffff !important;
  color: #202124;
}

/* Ensure all elements inside popup have white background */
#popup-content {
  background-color: #ffffff !important;
}

#popup-content > div {
  background-color: #ffffff !important;
}

/* Ensure all text elements have white background */
#popup-content p,
#popup-content h6,
#popup-content span,
#popup-content div {
  background-color: #ffffff !important;
}

/* Ensure buttons have proper background */
#popup-content .btn {
  background-color: inherit !important;
}

#popup-content .btn-primary {
  background-color: #1a73e8 !important;
  color: #ffffff !important;
}

#popup-content .btn-warning {
  background-color: #fbbc04 !important;
  color: #202124 !important;
}

#popup-content .btn-info {
  background-color: #34a853 !important;
  color: #ffffff !important;
}

#popup-content .btn-success {
  background-color: #34a853 !important;
  color: #ffffff !important;
}

/* Ensure cards and other containers have white background */
#popup-content .card,
#popup-content .card-body {
  background-color: #ffffff !important;
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
  border-top-color: #ffffff !important;
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
  z-index: 10001;
}

.ol-popup-closer:hover {
  color: #000;
}

/* Responsive */
/* Tablet Styles (max-width: 1024px) */
@media (max-width: 1024px){
  .gm-search-container{
    width: 450px;
    max-width: 450px;
  }
  
  .gm-left-sidebar{ 
    width: 280px; 
  }
  
  .map-fullscreen-container.gm-left-sidebar-open{ 
    padding-left: 280px; 
  }
  
  .map-fullscreen-container.gm-left-sidebar-open .gm-header{ 
    left: 280px; 
  }
  
  .map-fullscreen-container.gm-left-sidebar-open .gm-copyright{ 
    left: 280px; 
  }
  
  .gm-header-top{
    padding: 12px 16px;
    gap: 10px;
  }
  
  .gm-category-filters{
    gap: 8px;
    margin: 0 8px;
  }
  
  .gm-category-item{
    padding: 10px 16px;
    font-size: 15px;
  }
  
  .gm-layer-filter-btn{
    width: 70px;
    height: 70px;
  }
}

/* Tablet Styles (max-width: 768px) */
@media (max-width: 768px){
  :root{ 
    --gm-top-h: 140px; 
  }
  
  /* Show backdrop on tablet and mobile */
  .gm-sidebar-backdrop{
    display: block;
  }
  
  .gm-left-sidebar{ 
    width: 100%;
    max-width: 320px;
  }
  
  .map-fullscreen-container.gm-left-sidebar-open{ 
    padding-left: 0; 
  }
  
  .map-fullscreen-container.gm-left-sidebar-open .gm-header{ 
    left: 0; 
  }
  
  .map-fullscreen-container.gm-left-sidebar-open .gm-copyright{ 
    left: 0; 
  }
  
  .gm-header-top{
    height: auto;
    min-height: 80px;
    padding: 10px 12px;
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .gm-menu-btn{
    width: 44px;
    height: 44px;
    flex-shrink: 0;
  }
  
  .gm-menu-btn i{
    font-size: 26px;
  }
  
  .gm-search-container{
    flex: 1 1 auto;
    min-width: 0;
    width: auto;
    max-width: none;
    margin-left: 4px;
  }
  
  .gm-search-box{
    height: 48px;
    padding: 0 50px 0 50px;
    font-size: 16px;
  }
  
  .gm-search-icon{
    left: 16px;
    font-size: 22px;
  }
  
  .gm-directions-btn{
    width: 44px;
    height: 44px;
    right: 8px;
  }
  
  .gm-directions-btn i{
    font-size: 22px;
  }
  
  .gm-header-right{
    gap: 6px;
    flex-shrink: 0;
  }
  
  .gm-header-icon-btn{
    width: 44px;
    height: 44px;
  }
  
  .gm-header-icon-btn i{
    font-size: 26px;
  }
  
  .notification-badge {
    width: 7px;
    height: 7px;
    top: 6px;
    right: 6px;
  }
  
  .notification-pulse {
    width: 44px;
    height: 44px;
  }
  
  .gm-notification-btn::before {
    width: 44px;
    height: 44px;
  }
  
  .gm-profile-btn{
    width: 40px;
    height: 40px;
    font-size: 15px;
  }
  
  .gm-category-filters{
    order: 3;
    width: 100%;
    margin: 0;
    padding: 8px 12px 0;
    gap: 6px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .gm-category-item{
    padding: 8px 14px;
    font-size: 14px;
    gap: 8px;
    flex-shrink: 0;
  }
  
  .gm-category-item i{
    font-size: 20px;
  }
  
  .gm-map-controls{
    right: 12px;
    top: 12px;
  }
  
  .gm-control-btn{
    width: 36px;
    height: 36px;
  }
  
  .gm-control-btn.compass{
    width: 40px;
    height: 40px;
    margin-bottom: 6px;
  }
  
  .gm-control-btn.compass i{
    font-size: 18px;
  }
  
  .gm-layer-filter-container{
    bottom: 40px;
    left: 12px;
  }
  
  .gm-layer-filter-btn{
    width: 60px;
    height: 60px;
  }
  
  .gm-copyright{
    padding: 4px 8px;
    font-size: 9px;
  }
  
  .gm-sidebar-nav-item{
    padding: 10px 14px 10px 16px;
    font-size: 13px;
  }
  
  /* Popup responsive for tablet */
  .ol-popup{
    min-width: 180px;
    max-width: 90vw;
    padding: 12px;
    font-size: 14px;
  }
  
  #popup-content{
    font-size: 14px;
  }
  
  .ol-popup-closer{
    font-size: 16px;
    top: 4px;
    right: 6px;
  }
  
  .gm-recent-item{
    padding: 10px 14px 10px 16px;
  }
  
  .gm-recent-icon{
    width: 44px;
    height: 44px;
  }
  
  .gm-recent-title{
    font-size: 13px;
  }
  
  .gm-recent-subtitle{
    font-size: 11px;
  }
}

/* Mobile Styles (max-width: 640px) */
@media (max-width: 640px){
  :root{ 
    --gm-top-h: 160px; 
  }
  
  .gm-header-top{
    min-height: 100px;
    padding: 8px 10px;
    gap: 6px;
  }
  
  .gm-menu-btn{
    width: 40px;
    height: 40px;
  }
  
  .gm-menu-btn i{
    font-size: 24px;
  }
  
  .gm-search-container{
    order: 2;
    width: 100%;
    margin: 0;
    margin-top: 4px;
  }
  
  .gm-search-box{
    height: 44px;
    padding: 0 44px 0 44px;
    font-size: 15px;
    border-radius: 22px;
  }
  
  .gm-search-icon{
    left: 14px;
    font-size: 20px;
  }
  
  .gm-directions-btn{
    width: 40px;
    height: 40px;
    right: 6px;
  }
  
  .gm-directions-btn i{
    font-size: 20px;
  }
  
  .gm-header-right{
    order: 1;
    margin-left: 0;
    gap: 4px;
  }
  
  .gm-header-icon-btn{
    width: 40px;
    height: 40px;
  }
  
  .gm-header-icon-btn i{
    font-size: 24px;
  }
  
  .notification-badge {
    width: 6px;
    height: 6px;
    top: 5px;
    right: 5px;
  }
  
  .notification-pulse {
    width: 40px;
    height: 40px;
  }
  
  .gm-notification-btn::before {
    width: 40px;
    height: 40px;
  }
  
  .gm-profile-btn{
    width: 36px;
    height: 36px;
    font-size: 14px;
  }
  
  .gm-category-filters{
    order: 3;
    padding: 6px 10px 0;
    gap: 4px;
  }
  
  .gm-category-item{
    padding: 6px 12px;
    font-size: 13px;
    gap: 6px;
  }
  
  .gm-category-item i{
    font-size: 18px;
  }
  
  .gm-category-item span{
    display: none;
  }
  
  .gm-category-item:first-of-type span,
  .gm-category-item:nth-of-type(2) span,
  .gm-category-item:nth-of-type(3) span{
    display: inline;
  }
  
  .gm-left-sidebar{
    width: 100%;
    max-width: 100%;
    box-shadow: 4px 0 16px rgba(0,0,0,0.2);
  }
  
  .gm-map-controls{
    right: 8px;
    top: 8px;
  }
  
  .gm-control-btn{
    width: 32px;
    height: 32px;
  }
  
  .gm-control-btn.compass{
    width: 36px;
    height: 36px;
    margin-bottom: 4px;
  }
  
  .gm-control-btn.compass i{
    font-size: 16px;
  }
  
  .gm-control-btn i{
    font-size: 18px;
  }
  
  .gm-layer-filter-container{
    bottom: 35px;
    left: 8px;
  }
  
  .gm-layer-filter-btn{
    width: 50px;
    height: 50px;
  }
  
  .gm-copyright{
    padding: 3px 6px;
    font-size: 8px;
    line-height: 1.3;
  }
  
  /* Popup responsive for mobile */
  .ol-popup{
    min-width: 160px;
    max-width: 85vw;
    padding: 10px;
    font-size: 13px;
    bottom: 8px;
    left: -40px;
  }
  
  #popup-content{
    font-size: 13px;
  }
  
  #popup-content .btn{
    padding: 6px 12px;
    font-size: 12px;
  }
  
  .ol-popup-closer{
    font-size: 16px;
    top: 2px;
    right: 4px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .ol-popup:after{
    border-width: 8px;
    left: 40px;
  }
  
  .ol-popup:before{
    border-width: 9px;
    left: 40px;
  }
  
  .gm-sidebar-menu-btn{
    width: 36px;
    height: 36px;
    margin: 6px;
  }
  
  .gm-sidebar-menu-btn i{
    font-size: 22px;
  }
  
  .gm-sidebar-nav-item{
    padding: 10px 12px 10px 14px;
    font-size: 13px;
    gap: 12px;
  }
  
  .gm-sidebar-nav-item i{
    font-size: 18px;
  }
  
  .gm-recent-item{
    padding: 10px 12px 10px 14px;
    gap: 10px;
  }
  
  .gm-recent-icon{
    width: 40px;
    height: 40px;
  }
  
  .gm-recent-icon i{
    font-size: 18px;
  }
  
  .gm-recent-title{
    font-size: 13px;
  }
  
  .gm-recent-subtitle{
    font-size: 11px;
  }
  
  .gm-view-more{
    margin: 6px 10px;
    padding: 8px 10px;
    font-size: 13px;
  }
  
  .gm-get-app{
    padding: 12px 10px;
  }
  
  .gm-get-app-btn{
    padding: 8px 12px;
    font-size: 13px;
    gap: 10px;
  }
  
  .gm-layer-toggle{
    margin: 0 10px 10px;
    width: calc(100% - 20px);
    height: 36px;
  }
  
  #hazardMap{
    height: 100vh;
    height: 100dvh; /* Dynamic viewport height for mobile */
  }
}

/* Small Mobile Styles (max-width: 480px) */
@media (max-width: 480px){
  :root{ 
    --gm-top-h: 170px; 
  }
  
  .gm-header-top{
    min-height: 110px;
    padding: 6px 8px;
  }
  
  .gm-menu-btn{
    width: 36px;
    height: 36px;
  }
  
  .gm-menu-btn i{
    font-size: 22px;
  }
  
  .gm-search-box{
    height: 40px;
    padding: 0 40px 0 40px;
    font-size: 14px;
  }
  
  .gm-search-icon{
    left: 12px;
    font-size: 18px;
  }
  
  .gm-directions-btn{
    width: 36px;
    height: 36px;
    right: 4px;
  }
  
  .gm-directions-btn i{
    font-size: 18px;
  }
  
  .gm-header-icon-btn{
    width: 36px;
    height: 36px;
  }
  
  .gm-header-icon-btn i{
    font-size: 22px;
  }
  
  .notification-badge {
    width: 5px;
    height: 5px;
    top: 4px;
    right: 4px;
  }
  
  .notification-pulse {
    width: 36px;
    height: 36px;
  }
  
  .gm-notification-btn::before {
    width: 36px;
    height: 36px;
  }
  
  .gm-profile-btn{
    width: 32px;
    height: 32px;
    font-size: 13px;
  }
  
  .gm-category-item{
    padding: 5px 10px;
    font-size: 12px;
  }
  
  .gm-category-item i{
    font-size: 16px;
  }
  
  .gm-map-controls{
    right: 6px;
    top: 6px;
  }
  
  .gm-control-btn{
    width: 30px;
    height: 30px;
  }
  
  .gm-control-btn.compass{
    width: 34px;
    height: 34px;
  }
  
  .gm-control-btn.compass i{
    font-size: 15px;
  }
  
  .gm-control-btn i{
    font-size: 16px;
  }
  
  .gm-layer-filter-container{
    bottom: 30px;
    left: 6px;
  }
  
  .gm-layer-filter-btn{
    width: 45px;
    height: 45px;
  }
  
  .gm-copyright{
    font-size: 7px;
    padding: 2px 4px;
  }
  
  /* Popup responsive for small mobile */
  .ol-popup{
    min-width: 140px;
    max-width: 80vw;
    padding: 8px;
    font-size: 12px;
    bottom: 6px;
    left: -30px;
  }
  
  #popup-content{
    font-size: 12px;
  }
  
  #popup-content .btn{
    padding: 5px 10px;
    font-size: 11px;
  }
  
  .ol-popup-closer{
    font-size: 14px;
    width: 20px;
    height: 20px;
  }
  
  .ol-popup:after{
    border-width: 6px;
    left: 30px;
  }
  
  .ol-popup:before{
    border-width: 7px;
    left: 30px;
  }
}

</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@8.2.0/ol.css">
<link rel="stylesheet" href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}">
<!-- TourGuide JS CSS -->
<link rel="stylesheet" href="https://unpkg.com/@sjmc11/tourguidejs/dist/css/tour.min.css">
<style>
    /* Custom TourGuide Dialog Styles */
    [data-tg-dialog] {
        max-width: 380px !important;
        width: 100% !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3) !important;
        border-radius: 12px !important;
        overflow: hidden !important;
    }
    
    [data-tg-dialog-body] {
        padding: 20px !important;
        max-height: 400px;
        overflow-y: auto;
    }
    
    [data-tg-dialog-footer] {
        padding: 12px 20px !important;
        border-top: 1px solid #e5e7eb !important;
        background-color: #f9fafb !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    [data-tg-step-dots] {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 8px !important;
        margin-bottom: 8px !important;
        padding: 8px 0 !important;
        flex-wrap: wrap !important;
    }
    
    [data-tg-step-dot] {
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
        background-color: #d1d5db !important;
        border: none !important;
        padding: 0 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        margin: 0 2px !important;
    }
    
    [data-tg-step-dot][data-tg-active="true"] {
        background-color: #3b82f6 !important;
        width: 24px !important;
        border-radius: 4px !important;
    }
    
    [data-tg-dialog-footer] [data-tg-buttons] {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 10px !important;
        width: 100% !important;
    }
    
    [data-tg-button] {
        flex: 1 !important;
        padding: 10px 16px !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        font-size: 14px !important;
        border: 1px solid #e5e7eb !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        background-color: white !important;
        color: #374151 !important;
    }
    
    [data-tg-button][data-tg-button-primary] {
        background-color: #3b82f6 !important;
        color: white !important;
        border-color: #3b82f6 !important;
    }
    
    [data-tg-button][data-tg-button-primary]:hover {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
    }
    
    [data-tg-button]:not([data-tg-button-primary]):hover {
        background-color: #f9fafb !important;
    }
    
    [data-tg-step-progress] {
        font-size: 12px !important;
        color: #6b7280 !important;
        margin-bottom: 8px !important;
        text-align: center !important;
        width: 100% !important;
    }
    
    /* Alternative selectors for TourGuide JS */
    .tg-dialog,
    [class*="tg-dialog"] {
        max-width: 380px !important;
        width: 100% !important;
    }
    
    .tg-dialog-footer,
    [class*="tg-dialog-footer"] {
        padding: 12px 20px !important;
        border-top: 1px solid #e5e7eb !important;
        background-color: #f9fafb !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    .tg-step-dots,
    [class*="tg-step-dots"] {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 8px !important;
        margin-bottom: 8px !important;
        padding: 8px 0 !important;
        flex-wrap: wrap !important;
    }
    
    .tg-buttons,
    [class*="tg-buttons"] {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 10px !important;
        width: 100% !important;
    }
    
    .tg-button,
    [class*="tg-button"] {
        flex: 1 !important;
        padding: 10px 16px !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        font-size: 14px !important;
        border: 1px solid #e5e7eb !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
    }
</style>
@endsection

@section('content')











<!-- Full-Screen Map Container -->
<div class="map-fullscreen-container">
    <!-- Google Maps Style Header -->
    <div class="gm-header">
        <div class="gm-header-top">
            <!-- Hamburger Menu -->
            <button class="gm-menu-btn" id="gmMenuBtn" title="Menu">
                <i class="material-icons-outlined">menu</i>
            </button>
            
            <!-- Search Box -->
            <div class="gm-search-container">
                <i class="material-icons-outlined gm-search-icon">search</i>
                <input type="text" class="gm-search-box" id="gmSearchBox" placeholder="Search...">
                <button class="gm-directions-btn" id="gmDirectionsBtn" title="Directions">
                    <i class="material-icons-outlined">directions</i>
                </button>
            </div>
            
            <!-- Category Filters - Sejajar dengan Search Box -->
            <div class="gm-category-filters">
                  <a href="#" class="gm-category-item">
                    <i class="material-icons-outlined">camera_alt</i>
                    <span>CCTV</span>
                </a>
                 <a href="#" class="gm-category-item">
                    <i class="material-icons-outlined">assignment</i>
                    <span>SAP</span>
                </a>
                <a href="#" class="gm-category-item">
                    <i class="material-icons-outlined">people</i>
                    <span>Gps Orang</span>
                </a>
               
              
                <a href="#" class="gm-category-item">
                    <i class="material-icons-outlined">museum</i>
                    <span>Control Room</span>
                </a>
                <a href="#" class="gm-category-item">
                    <i class="material-icons-outlined">directions_bus</i>
                    <span>Gps Unit</span>
                </a>
                <a href="#" class="gm-category-item">
                    <i class="material-icons-outlined">local_pharmacy</i>
                    <span>Insiden</span>
                </a>
                <span class="gm-category-item">
                    <i class="material-icons-outlined category-arrow">chevron_right</i>
                </span>
            </div>
            
            <!-- Right Icons -->
            <div class="gm-header-right bg-white rounded-circle" style="position: relative;">
                <button class="gm-header-icon-btn gm-notification-btn" id="gmNotificationBtn" title="Notifications">
                    <i class="material-icons-outlined">notifications</i>
                    <span class="notification-badge"></span>
                    <span class="notification-pulse"></span>
                </button>
                
                <!-- Notification Panel -->
                <div class="gm-notification-panel" id="gmNotificationPanel">
                    <div class="gm-notification-panel-header">
                        <h3 class="gm-notification-panel-title">Ringkasan Matrix Risk</h3>
                        <div class="d-flex align-items-center gap-2">
                            <button class="gm-notification-panel-pin" id="gmNotificationPanelPin" title="Pin/Unpin Panel">
                                <i class="material-icons-outlined">push_pin</i>
                            </button>
                            <button class="gm-notification-panel-close" id="gmNotificationPanelClose">
                                <i class="material-icons-outlined">close</i>
                            </button>
                        </div>
                    </div>
                    <div class="gm-notification-panel-body" id="gmNotificationPanelBody">
                        <div class="gm-notification-empty">Memuat data...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Backdrop (for mobile) -->
    <div class="gm-sidebar-backdrop" id="gmSidebarBackdrop"></div>

    <!-- Google Maps Style Left Sidebar -->
    <div class="gm-left-sidebar collapsed" id="gmLeftSidebar">
        <!-- Hamburger Menu in Sidebar -->
        <button class="gm-sidebar-menu-btn" id="gmSidebarMenuBtn" title="Menu">
            <i class="material-icons-outlined">menu</i>
        </button>
        
        <!-- Navigation -->
        <div class="gm-sidebar-nav">
            <a href="#" class="gm-sidebar-nav-item">
                <i class="material-icons-outlined">bookmark_border</i>
                <span>Saved</span>
            </a>
            <a href="#" class="gm-sidebar-nav-item">
                <i class="material-icons-outlined">schedule</i>
                <span>Recents</span>
            </a>
        </div>
        
        <!-- Recents Section -->
        <div class="gm-sidebar-section">
            <div class="gm-sidebar-section-title">Recents</div>
            
            <!-- Recent Item 1 -->
            <div class="gm-recent-item">
                <div class="gm-recent-icon">
                    <i class="material-icons-outlined">location_on</i>
                    <span class="gm-recent-badge">8</span>
                </div>
                <div class="gm-recent-content">
                    <div class="gm-recent-title">Borneo</div>
                    <div class="gm-recent-subtitle">8 locations</div>
                </div>
            </div>
            
            <!-- Recent Item 2 -->
            <div class="gm-recent-item">
                <div class="gm-recent-icon">
                    <i class="material-icons-outlined">location_on</i>
                    <span class="gm-recent-badge">8</span>
                </div>
                <div class="gm-recent-content">
                    <div class="gm-recent-title">Borneo & Kaniung...</div>
                    <div class="gm-recent-subtitle">8 locations</div>
                </div>
            </div>
            
            <!-- View More -->
            <a href="#" class="gm-view-more">
                <span>View more</span>
                <i class="material-icons-outlined">more_vert</i>
            </a>
        </div>
        
        <!-- Layers Button -->
        <div style="padding: 16px; border-top: none; border-bottom: none;">
            <button class="gm-layer-toggle" id="gmSidebarLayerToggle" title="Layers" style="width: 100%; height: 40px; margin: 0; border: none;">
                <i class="material-icons-outlined" style="position: relative; z-index: 1; color: #5f6368;">layers</i>
            </button>
        </div>
        
        <!-- Get App -->
        <div class="gm-get-app">
            <a href="#" class="gm-get-app-btn">
                <i class="material-icons-outlined">phone_android</i>
                <span>Get app</span>
            </a>
        </div>
    </div>

    <!-- Map Controls Overlay -->
    <div class="map-controls-overlay">
        <!-- Backdrop -->
        <div id="backdrop" class="backdrop"></div>
        
        <!-- Top Bar -->
      
        
        <!-- Drawer -->
      
    </div>
    
    <!-- Google Maps Style JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle - Header Menu
            const gmMenuBtn = document.getElementById('gmMenuBtn');
            const gmSidebarMenuBtn = document.getElementById('gmSidebarMenuBtn');
            const gmLeftSidebar = document.getElementById('gmLeftSidebar');
            const gmSidebarBackdrop = document.getElementById('gmSidebarBackdrop');
            const mapContainer = document.querySelector('.map-fullscreen-container');
            
            function toggleSidebar() {
                if (gmLeftSidebar && mapContainer) {
                    const isCollapsed = gmLeftSidebar.classList.contains('collapsed');
                    
                    if (isCollapsed) {
                        // Open sidebar
                        gmLeftSidebar.classList.remove('collapsed');
                        mapContainer.classList.add('gm-left-sidebar-open');
                    } else {
                        // Close sidebar - full maps
                        gmLeftSidebar.classList.add('collapsed');
                        mapContainer.classList.remove('gm-left-sidebar-open');
                    }
                }
            }
            
            function closeSidebar() {
                if (gmLeftSidebar && mapContainer) {
                    gmLeftSidebar.classList.add('collapsed');
                    mapContainer.classList.remove('gm-left-sidebar-open');
                }
            }
            
            if (gmMenuBtn) {
                gmMenuBtn.addEventListener('click', toggleSidebar);
            }
            
            if (gmSidebarMenuBtn) {
                gmSidebarMenuBtn.addEventListener('click', toggleSidebar);
            }
            
            // Close sidebar when clicking backdrop (mobile)
            if (gmSidebarBackdrop) {
                gmSidebarBackdrop.addEventListener('click', closeSidebar);
            }

            // Search Box Focus
            const gmSearchBox = document.getElementById('gmSearchBox');
            if (gmSearchBox) {
                gmSearchBox.addEventListener('focus', function() {
                    this.style.boxShadow = '0 2px 8px 1px rgba(64,60,67,.24)';
                });
                
                gmSearchBox.addEventListener('blur', function() {
                    this.style.boxShadow = '0 2px 5px 1px rgba(64,60,67,.16)';
                });
            }

            // Drawer JavaScript (keep existing functionality)
            const drawer = document.getElementById('drawer');
            const backdrop = document.getElementById('backdrop');
            const btnMenu = document.getElementById('btnMenu');
            const btnClose = document.getElementById('btnClose');
            
            function openDrawer() {
                if (drawer) drawer.classList.add('open');
                if (backdrop) backdrop.classList.add('open');
            }
            
            function closeDrawer() {
                if (drawer) drawer.classList.remove('open');
                if (backdrop) backdrop.classList.remove('open');
            }
            
            if (btnMenu) {
                btnMenu.addEventListener('click', openDrawer);
            }
            
            if (btnClose) {
                btnClose.addEventListener('click', closeDrawer);
            }
            
            if (backdrop) {
                backdrop.addEventListener('click', closeDrawer);
            }
            
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeDrawer();
            });

            // Google Maps Style Zoom Controls Integration
            // This will be connected after map initialization
            window.gmZoomInBtn = document.getElementById('gmZoomInBtn');
            window.gmZoomOutBtn = document.getElementById('gmZoomOutBtn');
            window.gmCompassBtn = document.getElementById('gmCompassBtn');
            window.gmLayerToggle = document.getElementById('gmLayerToggle');
            
            if (window.gmZoomInBtn) {
                window.gmZoomInBtn.addEventListener('click', function() {
                    if (window.hazardMapView) {
                        const currentZoom = window.hazardMapView.getZoom();
                        window.hazardMapView.animate({
                            zoom: currentZoom + 1,
                            duration: 200
                        });
                    }
                });
            }
            
            if (window.gmZoomOutBtn) {
                window.gmZoomOutBtn.addEventListener('click', function() {
                    if (window.hazardMapView) {
                        const currentZoom = window.hazardMapView.getZoom();
                        window.hazardMapView.animate({
                            zoom: currentZoom - 1,
                            duration: 200
                        });
                    }
                });
            }
            
            if (window.gmCompassBtn) {
                window.gmCompassBtn.addEventListener('click', function() {
                    if (window.hazardMapView) {
                        window.hazardMapView.animate({
                            rotation: 0,
                            duration: 300
                        });
                    }
                });
            }
        });
    </script>
    
    <!-- Google Maps Style Right Controls -->
    <!-- <div class="gm-map-controls">
        <button class="gm-control-btn compass" id="gmCompassBtn" title="Reset bearing">
            <i class="material-icons-outlined">explore</i>
        </button>
        <button class="gm-control-btn zoom-in" id="gmZoomInBtn" title="Zoom in">
            <i class="material-icons-outlined">add</i>
        </button>
        <button class="gm-control-btn zoom-out" id="gmZoomOutBtn" title="Zoom out">
            <i class="material-icons-outlined">remove</i>
        </button>
        <button class="gm-control-btn street-view" id="gmStreetViewBtn" title="Street View">
            <i class="material-icons-outlined">streetview</i>
        </button>
        <div class="gm-layer-toggle" id="gmLayerToggle" title="Layers">
            <i class="material-icons-outlined" style="position: relative; z-index: 1; color: #5f6368; font-size: 18px;">layers</i>
        </div>
    </div> -->

 

    <!-- Map Container -->
    <div class="position-relative" style="width: 100%; height: 100vh; border: none !important; outline: none !important; margin: 0 !important; padding: 0 !important;">
        <div id="hazardMap" data-tg-tour="<strong>Interactive Map</strong><br><br>Peta interaktif untuk melihat lokasi semua data. Anda dapat:<br>• Zoom in/out dengan scroll mouse<br>• Klik marker untuk melihat detail<br>• Drag untuk menggeser peta<br>• Klik area kerja/CCTV untuk evaluasi" style="border: none !important; outline: none !important; margin: 0 !important; padding: 0 !important;"></div>
        <div id="popup" class="ol-popup">
            <a href="#" id="popup-closer" class="ol-popup-closer"></a>
            <div id="popup-content"></div>
        </div>
        
        <!-- Map Layer Filter (Bottom Left) -->
        <!-- Layer Filter - New Design -->
        <div class="gm-layer-wrap" id="gmLayerWrap">
            <!-- Toggle button -->
            <button type="button" class="gm-layer-btn" id="gmLayerToggleBtn" aria-expanded="false" title="Layers">
                <!-- Improved Layers Icon -->
                <svg class="gm-layer-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3.5 3.5 8.2 12 12.9 20.5 8.2 12 3.5Z" />
                    <path d="M3.5 12.0 12 16.7 20.5 12.0" />
                    <path d="M3.5 15.8 12 20.5 20.5 15.8" />
                </svg>
                <span class="layer-text">Layers</span>
            </button>
            <!-- Panel (Bootstrap Collapse) -->
            <div class="collapse" id="gmLayerPanel">
                <div class="gm-layer-panel">
                    <div class="gm-layer-scroll">
                        <input class="btn-check" type="checkbox" id="layerSatellite" autocomplete="off" checked>
                        <label class="gm-tile" for="layerSatellite" data-layer="satellite">
                            <div class="gm-thumb" style="background-image:url('https://images.unsplash.com/photo-1617897711385-df9c86b7dfe3?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');"></div>
                            <div class="gm-label">Matriks CCTV</div>
                            <div class="gm-sub">CCTV MAPS</div>
                        </label>
                        <input class="btn-check" type="checkbox" id="layerTerrain" autocomplete="off">
                        <label class="gm-tile" for="layerTerrain" data-layer="terrain">
                            <div class="gm-thumb" style="background-image:url('https://images.unsplash.com/photo-1622645636770-11fbf0611463?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');"></div>
                            <div class="gm-label">Unit dan Orang</div>
                            <div class="gm-sub">UNIT MAPS</div>
                        </label>
                        <input class="btn-check" type="checkbox" id="layerTraffic" autocomplete="off">
                        <label class="gm-tile" for="layerTraffic" data-layer="traffic">
                            <div class="gm-thumb" style="background-image:url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=600&q=60');"></div>
                            <div class="gm-label">Matriks Area Kerja</div>
                            <div class="gm-sub">AREA KEJA MAPS</div>
                        </label>
                        <input class="btn-check" type="checkbox" id="layerTransit" autocomplete="off">
                        <label class="gm-tile" for="layerTransit" data-layer="transit">
                            <div class="gm-thumb" style="background-image:url('https://images.unsplash.com/photo-1530677003768-e25c2b121303?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');"></div>
                            <div class="gm-label">Matriks Sub Ketidaksesuaian SAP</div>
                        <div class="gm-sub">HAZARD MAPS</div>
                        </label>
                       
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Icon Toolbar - Left Side -->
        {{-- <div class="map-icon-toolbar" data-tg-tour="<strong>Icon Toolbar</strong><br><br>Gunakan icon untuk melihat data berbeda:<br>• CCTV: Daftar kamera CCTV<br>• SAP: Safety Action Plan<br>• Insiden: Data insiden kecelakaan<br>• Unit: Posisi unit kendaraan<br>• GPS Orang: Lokasi personel<br>• Control Room: Data control room<br>• PJA: Pre Job Analysis<br>• Evaluasi: Summary evaluasi area">
            <button class="icon-toolbar-btn active" data-tab="cctv" title="CCTV" id="iconToolbarCctv">
                <i class="material-icons-outlined">videocam</i>
                <span class="btn-label">CCTV</span>
                <span class="icon-toolbar-badge" id="iconToolbarCctvCount">0</span>
            </button>
            <button class="icon-toolbar-btn" data-tab="sap" title="Safety Action Plan" id="iconToolbarSap">
                <i class="material-icons-outlined">assignment</i>
                <span class="btn-label">SAP</span>
                <span class="icon-toolbar-badge" id="iconToolbarSapCount">0</span>
            </button>
            <button class="icon-toolbar-btn" data-tab="insiden" title="Insiden" id="iconToolbarInsiden">
                <i class="material-icons-outlined">report_problem</i>
                <span class="btn-label">Insiden</span>
                <span class="icon-toolbar-badge" id="iconToolbarInsidenCount">0</span>
            </button>
            <button class="icon-toolbar-btn" data-tab="unit" title="Unit Kendaraan" id="iconToolbarUnit">
                <i class="material-icons-outlined">directions_car</i>
                <span class="btn-label">Unit</span>
                <span class="icon-toolbar-badge" id="iconToolbarUnitCount">0</span>
            </button>
            <div class="icon-toolbar-divider"></div>
            <button class="icon-toolbar-btn" data-tab="controlroom" title="Control Room" id="iconToolbarControlroom">
                <i class="material-icons-outlined">meeting_room</i>
                <span class="btn-label">Control</span>
                <span class="icon-toolbar-badge" id="iconToolbarControlroomCount">0</span>
            </button>
            <button class="icon-toolbar-btn" data-tab="pja" title="Pre Job Analysis" id="iconToolbarPja">
                <i class="material-icons-outlined">description</i>
                <span class="btn-label">PJA</span>
                <span class="icon-toolbar-badge" id="iconToolbarPjaCount">0</span>
            </button>
            <button class="icon-toolbar-btn" data-tab="evaluasi" title="Evaluasi" id="iconToolbarEvaluasi">
                <i class="material-icons-outlined">assessment</i>
                <span class="btn-label">Evaluasi</span>
            </button>
        </div> --}}
        
        <!-- Sidebar Panel - Right Side -->
        {{-- <div id="mapSidebar" class="map-sidebar" data-tg-tour="<strong>Sidebar Panel</strong><br><br>Panel sidebar menampilkan daftar data berdasarkan tab yang dipilih. Gunakan icon toolbar di kiri untuk beralih antara CCTV, SAP, Insiden, Unit, GPS Orang, Control Room, PJA, dan Evaluasi. Gunakan search untuk mencari data spesifik."> --}}
            <!-- Toggle Button -->
         
            
            <!-- Sidebar Content -->
            {{-- <div class="sidebar-content">
                            
                            <!-- Tab Content -->
                            <div class="sidebar-body">
                                <!-- Search Bar -->
                                <div class="sidebar-search" data-tg-tour="<strong>Search & Filter</strong><br><br>Gunakan search bar untuk mencari data spesifik di tab yang aktif. Klik tombol filter untuk opsi filter tambahan.">
                                    <i class="material-icons-outlined search-icon">search</i>
                                    <input type="text" id="sidebarSearchInput" class="sidebar-search-input" placeholder="Cari...">
                                    <button type="button" class="sidebar-filter-btn" id="sidebarFilterBtn" title="Filter">
                                        <i class="material-icons-outlined">tune</i>
                                    </button>
                                </div>
                                
                                <!-- List Container -->
                                <div class="sidebar-list-container">
                                    <!-- CCTV Tab Content -->
                                    <div class="tab-content active" id="tabContentCctv">
                                        <div class="sidebar-list" id="cctvList"></div>
                                    </div>
                                    
                                    <!-- SAP Tab Content -->
                                    <div class="tab-content" id="tabContentSap">
                                        <!-- Week Filter -->
                                        <div class="sidebar-week-filter" style="padding: 12px; border-bottom: 1px solid #e5e7eb; background: #f8f9fa;">
                                            <label style="font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 8px; display: block;">Filter Week (Senin-Senin)</label>
                                            <input type="week" id="sapWeekFilter" class="form-control form-control-sm" style="font-size: 12px;">
                                            <div style="margin-top: 8px; font-size: 11px; color: #9ca3af;">
                                                <span id="sapWeekRange">Week: -</span>
                                            </div>
                                        </div>
                                        <div class="sidebar-list" id="sapList"></div>
                                    </div>
                                    
                                    <!-- Insiden Tab Content -->
                                    <div class="tab-content" id="tabContentInsiden">
                                        <div class="sidebar-list" id="insidenList"></div>
                                    </div>
                                    
                                    <!-- Unit Tab Content -->
                                    <div class="tab-content" id="tabContentUnit">
                                        <div class="sidebar-list" id="unitList"></div>
                                    </div>
                                    
                                    <!-- Control Room Tab Content -->
                                    <div class="tab-content" id="tabContentControlroom">
                                        <div class="sidebar-list" id="controlroomList"></div>
                                    </div>
                                    
                                    <!-- PJA Tab Content -->
                                    <div class="tab-content" id="tabContentPja">
                                        <div class="sidebar-list" id="pjaList"></div>
                                    </div>
                                    
                                    <!-- Evaluasi Tab Content -->
                                    <div class="tab-content" id="tabContentEvaluasi">
                                        <div id="evaluasiContent" style="padding: 16px;">
                                            <div style="text-align: center; color: #6b7280; padding: 40px 20px;">
                                                <i class="material-icons-outlined" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;">assessment</i>
                                                <p style="margin: 0; font-size: 14px;">Klik area kerja atau area CCTV di peta untuk melihat summary evaluasi</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            </div> --}}
                    {{-- </div> --}}
    </div>
</div>
</div>

<!-- Modal Detail SAP -->
<div class="modal fade" id="sapDetailModal" tabindex="-1" aria-labelledby="sapDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sapDetailModalLabel">Detail SAP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="sapDetailModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Summary Area Kerja -->
<div class="modal fade" id="areaKerjaSummaryModal" tabindex="-1" aria-labelledby="areaKerjaSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0" id="areaKerjaSummaryModalLabel">
                    <i class="material-icons-outlined">work</i> Summary Area Kerja
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary btn-sm" id="btnIntervensiAreaKerja" title="Kirim Intervensi">
                        <i class="material-icons-outlined" style="font-size: 18px; vertical-align: middle;">send</i>
                        <span>Intervensi</span>
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body" id="areaKerjaSummaryModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Intervensi Area Kerja -->
<div class="modal fade" id="intervensiAreaKerjaModal" tabindex="-1" aria-labelledby="intervensiAreaKerjaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="intervensiAreaKerjaModalLabel">
                    <span class="material-icons-outlined me-2">send</span>
                    Form Intervensi Area Kerja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="intervensiAreaKerjaForm">
                    <input type="hidden" id="intervensiAreaKerja" name="area_kerja" value="">
                    <input type="hidden" id="intervensiLokasi" name="lokasi" value="">
                    
                    
                    <div class="mb-3">
                        <label for="intervensiLokasiDisplay" class="form-label fw-semibold">Lokasi</label>
                        <input type="text" class="form-control" id="intervensiLokasiDisplay" readonly>
                    </div>
                    
                    
                    <div class="mb-3">
                        <label for="intervensiPICAreaKerja" class="form-label fw-semibold">PIC (Pengawas) <span class="text-danger">*</span></label>
                        <select class="form-select" id="intervensiPICAreaKerja" name="pic" required>
                            <option value="">Pilih PIC...</option>
                        </select>
                        <div class="form-text">Pilih PIC (Pengawas) dari daftar pengguna</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="intervensiIssueAreaKerja" class="form-label fw-semibold">Issue <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="intervensiIssueAreaKerja" name="issue" rows="5" placeholder="Masukkan issue atau masalah yang ditemukan..." required></textarea>
                        <div class="form-text">Jelaskan issue atau masalah yang memerlukan intervensi</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitIntervensiAreaKerjaBtn">
                    <span class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">send</span>
                    Kirim Intervensi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Control Room -->
<div class="modal fade" id="controlRoomModal" tabindex="-1" aria-labelledby="controlRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="controlRoomModalLabel">
                    <i class="material-icons-outlined">museum</i> Control Room
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="controlRoomModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="applyControlRoomFilter()">Terapkan Filter</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal CCTV Stream -->
<div class="modal fade" id="cctvStreamModal" tabindex="-1" aria-labelledby="cctvStreamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header bg-dark border-secondary">
                <h5 class="modal-title text-white" id="cctvStreamModalLabel">
                    <i class="material-icons-outlined me-2">videocam</i> Live Stream
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-dark p-0 position-relative" style="min-height: 400px;">
                <div id="cctvStreamLoading" class="position-absolute top-50 start-50 translate-middle text-center text-white" style="z-index: 10;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Memuat stream video...</p>
                </div>
                <iframe id="cctvStreamFrame" 
                    style="width: 100%; height: 70vh; border: none; display: none;"
                    allowfullscreen
                    allow="autoplay; fullscreen">
                </iframe>
                <video id="cctvStreamVideo" 
                    style="width: 100%; height: 70vh; display: none;"
                    controls
                    autoplay
                    muted>
                </video>
            </div>
            <div class="modal-footer bg-dark border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="refreshCurrentStream()">
                    <i class="material-icons-outlined me-1" style="font-size: 16px; vertical-align: middle;">refresh</i>
                    Refresh
                </button>
            </div>
        </div>
    </div>
</div>




@endsection

@section('scripts')
<!-- Ensure jQuery is loaded first (from vendor-scripts) -->
<script>
    // Wait for jQuery to be available
    if (typeof jQuery === 'undefined' && typeof $ === 'undefined') {
        console.warn('jQuery is not loaded yet, waiting...');
        // jQuery should be loaded from vendor-scripts.blade.php
    }
</script>
<!-- Select2 JS for PIC dropdown (must be loaded after jQuery) -->
<script src="{{ URL::asset('build/plugins/select2/js/select2.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ol@8.2.0/dist/ol.js"></script>
<script src="https://cdn.jsdelivr.net/npm/proj4@2.9.0/dist/proj4.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.7/dist/hls.min.js"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<!-- Load BMO2 PAMA GeoJSON data -->
<script src="{{ asset('js/area-kerja-bmo2-pama.js') }}"></script>
{{-- <script src="{{ asset('js/area-cctv-bmo2-pama.js') }}"></script> --}}
{{-- <script src="{{ asset('js/difference_bmo2-pama.js') }}"></script> --}}
{{-- <script src="{{ asset('js/symmetrical_difference_bmo2-pama.js') }}"></script> --}}
{{-- <script src="{{ asset('js/intersection_bmo2-pama.js') }}"></script> --}}

<!-- Load Area CCTV GeoJSON data -->
{{-- <script src="{{ asset('js/area_cctv_bmo1_fad.js') }}"></script>
<script src="{{ asset('js/area_cctv_bmo1_kdc.js') }}"></script>
<script src="{{ asset('js/area_cctv_bmo2_buma.js') }}"></script>
<script src="{{ asset('js/area_cctv_bmo2_pama.js') }}"></script>
<script src="{{ asset('js/area_cctv_bmo3_bar.js') }}"></script>
<script src="{{ asset('js/area_cctv_gmo_kdc.js') }}"></script>
<script src="{{ asset('js/area_cctv_gmo_pama.js') }}"></script>
<script src="{{ asset('js/area_cctv_lmo_buma.js') }}"></script>
<script src="{{ asset('js/area_cctv_lmo_fad.js') }}"></script>
<script src="{{ asset('js/area_cctv_smo_mtn.js') }}"></script> --}}

<!-- Load Area Kerja GeoJSON data -->
<script src="{{ asset('js/area_kerja_bmo1_fad.js') }}"></script>
<script src="{{ asset('js/area_kerja_bmo1_kdc.js') }}"></script>
<script src="{{ asset('js/area_kerja_bmo2_buma.js') }}"></script>
<script src="{{ asset('js/area_kerja_bmo3_bar.js') }}"></script>
<script src="{{ asset('js/area_kerja_gmo_kdc.js') }}"></script>
<script src="{{ asset('js/area_kerja_gmo_pama.js') }}"></script>
<script src="{{ asset('js/area_kerja_lmo_buma.js') }}"></script>
<script src="{{ asset('js/area_kerja_lmo_fad.js') }}"></script>
<script src="{{ asset('js/area_kerja_smo_mtn.js') }}"></script>

<script>
    // Calculate and update area kerja and CCTV coverage
    function calculateAreaCoverage() {
        try {
            // Check if data is available
            if (typeof window.areaKerjaGeoJsonDataPama === 'undefined' || 
                typeof window.areaCctvGeoJsonDataBmo2Pama === 'undefined') {
                console.log('Waiting for GeoJSON data to load...');
                setTimeout(calculateAreaCoverage, 200);
                return;
            }
            
            // Get area kerja data
            const areaKerjaData = window.areaKerjaGeoJsonDataPama;
            // Get CCTV data
            const areaCctvData = window.areaCctvGeoJsonDataBmo2Pama;
            
            // Calculate total luasan area kerja
            let totalLuasanAreaKerja = 0;
            if (areaKerjaData && areaKerjaData.features && Array.isArray(areaKerjaData.features)) {
                areaKerjaData.features.forEach(feature => {
                    if (feature.properties && feature.properties.luasan) {
                        totalLuasanAreaKerja += parseFloat(feature.properties.luasan) || 0;
                    }
                });
            }
            
            // Calculate total luasan CCTV and count
            let totalLuasanCctv = 0;
            let totalCctvCount = 0;
            if (areaCctvData && areaCctvData.features && Array.isArray(areaCctvData.features)) {
                totalCctvCount = areaCctvData.features.length;
                areaCctvData.features.forEach(feature => {
                    if (feature.properties && feature.properties.luasan) {
                        totalLuasanCctv += parseFloat(feature.properties.luasan) || 0;
                    }
                });
            }
            
            // Calculate coverage percentage
            let coveragePercentage = 0;
            if (totalLuasanAreaKerja > 0) {
                coveragePercentage = (totalLuasanCctv / totalLuasanAreaKerja) * 100;
            }
            
            // Format luasan function
            function formatLuasan(luasan) {
                if (luasan >= 1000000) {
                    return (luasan / 1000000).toFixed(2) + ' km²';
                } else if (luasan >= 10000) {
                    return (luasan / 10000).toFixed(2) + ' ha';
                } else {
                    return luasan.toLocaleString('id-ID', { maximumFractionDigits: 2 }) + ' m²';
                }
            }
            
            // Update UI elements
            const luasanAreaKerjaEl = document.getElementById('luasanAreaKerja');
            const luasanCctvEl = document.getElementById('luasanCctv');
            const totalCctvCountEl = document.getElementById('totalCctvCount');
            const coveragePercentageEl = document.getElementById('coveragePercentage');
            const coverageBadgeEl = document.getElementById('coverageBadge');
            
            // Update Luasan Area Kerja
            if (luasanAreaKerjaEl) {
                luasanAreaKerjaEl.textContent = formatLuasan(totalLuasanAreaKerja);
            }
            
            // Update Luasan CCTV
            if (luasanCctvEl) {
                luasanCctvEl.textContent = formatLuasan(totalLuasanCctv);
            }
            
            // Update Total CCTV Count
            if (totalCctvCountEl) {
                totalCctvCountEl.textContent = totalCctvCount.toLocaleString('id-ID');
            }
            
            // Update Coverage Percentage
            if (coveragePercentageEl) {
                coveragePercentageEl.textContent = coveragePercentage.toFixed(2) + '%';
            }
            
            // Update Coverage Badge
            if (coverageBadgeEl) {
                coverageBadgeEl.textContent = coveragePercentage.toFixed(2) + '% Coverage';
            }
            
            console.log('Area Coverage Calculated:', {
                totalLuasanAreaKerja: totalLuasanAreaKerja.toLocaleString('id-ID'),
                totalLuasanCctv: totalLuasanCctv.toLocaleString('id-ID'),
                totalCctvCount: totalCctvCount,
                coveragePercentage: coveragePercentage.toFixed(2) + '%'
            });
        } catch (error) {
            console.error('Error calculating area coverage:', error);
        }
    }
    
    // Run calculation when DOM is ready and data is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit for GeoJSON files to load
            setTimeout(calculateAreaCoverage, 300);
        });
    } else {
        // DOM already loaded, wait for GeoJSON files
        setTimeout(calculateAreaCoverage, 300);
    }
</script>
<script>
    // SAP (Safety Action Plan) data dari ClickHouse
    // Mengganti hazard dengan SAP dari tabel nitip.union_sap_all_with_karyawan_full
    // Data ini akan di-overwrite oleh loadSapDataByWeek() berdasarkan week filter
    // Filter hanya SAP hari ini untuk performa (mengurangi lag)
    let allSapData = @json($sapData ?? []);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const todayStr = today.toISOString().split('T')[0]; // Format: YYYY-MM-DD
    
    // OPTIMIZED: Filter hanya SAP hari ini dengan early exit untuk performa
    // Sidebar: hanya data hari ini, Map: maksimal 1000 terbaru
    // Deklarasi di scope global agar bisa diakses oleh fungsi lain
    var sapDataForSidebar = []; // Data hari ini untuk sidebar
    var sapData = []; // Data terbatas untuk map (1000 terbaru)
    var sapDataAllWeek = []; // Semua data per week (untuk count di tab)
    
    if (allSapData && allSapData.length > 0) {
        // Filter data hari ini (semua data, tidak dibatasi)
        const todaySapData = [];
        for (let i = 0; i < allSapData.length; i++) {
            const sap = allSapData[i];
            if (!sap.tanggal_pelaporan && !sap.detected_at) continue;
            
            try {
                const sapDate = new Date(sap.tanggal_pelaporan || sap.detected_at);
                sapDate.setHours(0, 0, 0, 0);
                const sapDateStr = sapDate.toISOString().split('T')[0];
                if (sapDateStr === todayStr) {
                    todaySapData.push(sap);
                }
            } catch (e) {
                // Skip invalid dates
                continue;
            }
        }
        
        // Urutkan berdasarkan tanggal terbaru untuk sidebar
        sapDataForSidebar = todaySapData.sort((a, b) => {
            const dateA = new Date(a.tanggal_pelaporan || a.detected_at || 0);
            const dateB = new Date(b.tanggal_pelaporan || b.detected_at || 0);
            return dateB - dateA; // Terbaru di atas
        });
        
        // Untuk data awal, sapDataAllWeek juga diisi dengan data hari ini (akan di-overwrite saat week filter dipilih)
        sapDataAllWeek = [...sapDataForSidebar];
        
        // Map: Hanya ambil 1000 terbaru
        sapData = sapDataForSidebar.slice(0, 1000);
    }
    
    console.log(`Filtered SAP data: Sidebar (today) ${sapDataForSidebar.length} items | Map ${sapData.length} items (limited to 1000) for today (${todayStr}) out of ${allSapData.length} total`);
    
    const hazardDetections = sapData; // Alias untuk kompatibilitas dengan kode yang sudah ada
    
    // Data CCTV diambil langsung dari database (tabel cctv_data_bmo2), bukan dari WMS atau GeoJSON
    // cctvLocations: Data CCTV yang sudah difilter berdasarkan auth pengawas (DEFAULT)
    // cctvLocationsForMap: Hanya CCTV yang punya koordinat dan sudah difilter pengawas (DEFAULT)
    // cctvLocationsForControlRoom: SEMUA CCTV tanpa filter pengawas (untuk filter Control Room)
    // cctvLocationsForMapAll: SEMUA CCTV dengan koordinat tanpa filter pengawas (untuk filter Control Room)
    const cctvLocations = @json($cctvLocations ?? []);
    const cctvLocationsForControlRoom = @json($cctvLocationsForControlRoom ?? []);
    const cctvLocationsForMapAll = @json($cctvLocationsForMapAll ?? []);
    const cctvLocationsForMap = @json($cctvLocationsForMap ?? $cctvLocations);
    
    const grDetections = @json($grDetections ?? []);
    const insidenDataset = @json($insidenGroups);
    const insidenDatasetMap = new Map(insidenDataset.map(item => [item.no_kecelakaan, item]));
    let unitVehicles = @json($unitVehicles ?? []);
    const luasanComparisons = [];
    let currentHlsInstance = null;
    const defaultCctvRtspUrl = 'rtsp://gkr:Berau2025!@10.1.162.180:554/hocomdev';
    
    // Python App Configuration
    const pythonAppUrl = 'http://localhost:5000';
    
    // Current stream data for refresh functionality
    let currentStreamData = {
        cctvName: null,
        rtspUrl: null
    };

    // WMS Server Configuration
    const wmsServers = {
        smo: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_SMO_BLOCK_B1_2510/MapServer/WMSServer',
            name: 'SMO Block B1',
            bbox: [117.402228, 2.150819, 117.505579, 2.221687],
            center: [117.4539035, 2.186253]
        },
        smoA: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_SMO_BLOCK_A/MapServer/WMSServer',
            name: 'SMO Block A',
            bbox: [117.378740, 2.154163, 117.409737, 2.199252],
            center: [117.3942385, 2.1767075]
        },
        smoBEastWest: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_SMO_BLOCK_B_EAST_WEST/MapServer/WMSServer',
            name: 'SMO Block B East-West',
            bbox: [117.333284, 2.166645, 117.420828, 2.333354],
            center: [117.377056, 2.2499995]
        },
        bmo: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_BMO_BLOCK_1_4/MapServer/WMSServer',
            name: 'BMO Block 1-4',
            bbox: [117.437891, 2.026662, 117.483348, 2.122948],
            center: [117.4606195, 2.074805]
        },
        bmo56: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_BMO_BLOCK_5_6/MapServer/WMSServer',
            name: 'BMO Block 5-6',
            bbox: [117.405839, 1.971650, 117.475021, 2.095264],
            center: [117.44043, 2.033457]
        },
        bmo7: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_BMO_BLOCK_7/MapServer/WMSServer',
            name: 'BMO Block 7',
            bbox: [117.312358, 1.941393, 117.426208, 2.036692],
            center: [117.369283, 1.9890425]
        },
        bmo8: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_BMO_BLOCK_8/MapServer/WMSServer',
            name: 'BMO Block 8',
            bbox: [117.143050, 1.873312, 117.353350, 2.000030],
            center: [117.2482, 1.936671]
        },
        bmo9: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_BMO_BLOCK_9/MapServer/WMSServer',
            name: 'BMO Block 9',
            bbox: [117.129991, 1.936764, 117.183360, 2.043340],
            center: [117.1566755, 1.990052]
        },
        bmo10: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_BMO_BLOCK_10/MapServer/WMSServer',
            name: 'BMO Block 10',
            bbox: [117.166646, 2.033321, 117.241680, 2.132808],
            center: [117.204163, 2.0830645]
        },
        bmoParapatan: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_BMO_BLOCK_PARAPATAN/MapServer/WMSServer',
            name: 'BMO Block Parapatan',
            bbox: [117.431663, 2.090234, 117.483621, 2.146128],
            center: [117.457642, 2.118181]
        },
        gurimbang: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_GURIMBANG/MapServer/WMSServer',
            name: 'Gurimbang',
            bbox: [117.483325, 2.091636, 117.625039, 2.212991],
            center: [117.554182, 2.1523135]
        },
        khdtk: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_KHDTK/MapServer/WMSServer',
            name: 'KHDTK',
            bbox: [117.175827, 1.900871, 117.241266, 1.948915],
            center: [117.2085465, 1.924893]
        },
        punan: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_PUNAN/MapServer/WMSServer',
            name: 'Punan',
            bbox: [117.204989, 2.166649, 117.333347, 2.248363],
            center: [117.269168, 2.207506]
        },
        lati: {
            url: 'https://sgi.beraucoal.co.id/server/services/Basemap_Layer_LATI_2510/MapServer/WMSServer',
            name: 'Lati',
            bbox: [117.509916, 2.189945, 117.638408, 2.418367],
            center: [117.574162, 2.304156]
        }
    };
    
    // Current WMS server
    let currentWmsServer = 'smo';
    let wmsUrl = wmsServers[currentWmsServer].url;
    let currentLayer = '';
    let wmsLayer = null;
    let hazardLayer = null;
    let cctvLayer = null;
    let grLayer = null;
    let insidenLayer = null;
    let unitVehicleLayer = null;
    let userGpsLayer = null;
    let dailyOperationPlansLayer = null;
    let popupOverlay = null;
    
    // Site filter - harus didefinisikan sebelum digunakan di style function
    let currentSiteFilter = '';
    
    // Sidebar Panel Management - harus didefinisikan sebelum digunakan
    let currentSidebarTab = 'cctv';
    let sidebarCollapsed = false;
    let filteredSidebarData = {
        cctv: [],
        sap: [],
        insiden: [],
        unit: [],
        // gps: [], // Removed - no longer used
        controlroom: [],
        pja: []
    };
    
    // Store original PJA data for filtering
    let originalPjaData = [];
    
    // Store original Control Room data for filtering
    let originalControlRoomData = [];
    
    // Layer visibility state
    // Default: SAP, Unit, dan GPS Orang hidden untuk performa
    let layerVisibility = {
        cctv: true,
        hazard: false,  // SAP default hidden
        gr: true,
        insiden: true,
        unit: false,    // Unit default hidden
        gps: false      // GPS Orang default hidden
    };
    
    // SAP toggle state - track if SAP is currently loaded and visible
    let sapDataLoaded = false;
    let sapDataCache = []; // Cache untuk menyimpan data SAP yang sudah di-load
    
    // Unit Vehicle toggle state - track if Unit is currently visible
    let unitDataLoaded = false;
    
    // GPS Orang toggle state - track if GPS Orang is currently visible
    let gpsOrangDataLoaded = false;
    let gpsOrangDataCache = []; // Cache untuk menyimpan data GPS Orang yang sudah di-load
    
    // Control Room filter state
    let activeControlRooms = []; // Array of control room names that are currently active
    let allControlRooms = []; // Store all control rooms data
    
    // BMO2 PAMA GeoJSON layers
    let areaKerjaBmo2PamaLayer = null;
    let areaCctvBmo2PamaLayer = null;
    let differenceBmo2PamaLayer = null;
    let symmetricalDifferenceBmo2PamaLayer = null;
    let intersectionBmo2PamaLayer = null;
    
    // Highlight layer for selected area kerja / luasan
    let highlightedAreaKerjaLayer = null;
    let highlightedLuasanLayer = null;

    // Create Google Satellite tile source (fallback)
    // const googleSatelliteSource = new ol.source.XYZ({
    //     url: 'http://mt0.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}',
    //     attributions: '© Google',
    //     maxZoom: 20
    // });

    const googleSatelliteSource = new ol.source.XYZ({
        url: 'http://mt0.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}',
        attributions: '© Google',
        maxZoom: 20
    });

    // const googleSatelliteSource = new ol.source.XYZ({
    //     url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    //     attributions: 'Tiles © Esri',
    //     maxZoom: 19
    //     });

    // const googleSatelliteSource = new ol.source.XYZ({
    //     url: 'https://mt0.google.com/vt/lyrs=p&hl=en&x={x}&y={y}&z={z}',
    //     attributions: '© Google',
    //     maxZoom: 20
    //     });

    // const googleSatelliteSource = new ol.source.XYZ({
    //     url: 'https://{a-d}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png',
    //     attributions: '© OpenStreetMap contributors, © CARTO',
    //     maxZoom: 20
    //     });

        // const googleSatelliteSource = new ol.source.XYZ({
        //     url: 'https://{a-c}.tile.opentopomap.org/{z}/{x}/{y}.png',
        //     attributions: '© OpenStreetMap contributors, © OpenTopoMap',
        //     maxZoom: 17
        //     });

//         const googleSatelliteSource = new ol.source.XYZ({
            //   url: 'https://tiles.stadiamaps.com/tiles/stamen_toner/{z}/{x}/{y}.png?api_key=YOUR_KEY',
            //   attributions: '© Stadia Maps © Stamen Design © OpenMapTiles © OpenStreetMap contributors',
            //   maxZoom: 20
            // });

// const googleSatelliteSource = new ol.source.XYZ({
//   url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
//   attributions: '© OpenStreetMap contributors',
//   maxZoom: 19
// });

// const googleSatelliteSource = new ol.source.XYZ({
//   url: 'https://{a-c}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
//   attributions: '© OpenStreetMap contributors, HOT',
//   maxZoom: 19
// });







    // Function to create layer from GeoJSON data (for CRS84/EPSG:4326 data)
    function createLayerFromGeoJson(geoJsonData, layerName, styleFunction, zIndex = 300) {
        if (!geoJsonData) {
            console.warn(`${layerName}: GeoJSON data is null or undefined`);
            return new ol.layer.Vector({
                source: new ol.source.Vector(),
                name: layerName,
                zIndex: zIndex,
                visible: true
            });
        }

        if (!geoJsonData.features || geoJsonData.features.length === 0) {
            console.warn(`${layerName}: GeoJSON data has no features (features: ${geoJsonData.features ? geoJsonData.features.length : 'null'})`);
            return new ol.layer.Vector({
                source: new ol.source.Vector(),
                name: layerName,
                zIndex: zIndex,
                visible: true
            });
        }

        try {
            console.log(`${layerName}: Parsing ${geoJsonData.features.length} features...`);
            const features = new ol.format.GeoJSON().readFeatures(geoJsonData, {
                dataProjection: 'EPSG:4326',
                featureProjection: 'EPSG:3857'
            });

            console.log(`${layerName}: Successfully parsed ${features.length} features`);

            return new ol.layer.Vector({
                source: new ol.source.Vector({
                    features: features
                }),
                style: styleFunction,
                name: layerName,
                zIndex: zIndex,
                visible: true
            });
        } catch (error) {
            console.error(`${layerName}: Error parsing GeoJSON:`, error);
            console.error('GeoJSON data sample:', JSON.stringify(geoJsonData).substring(0, 200));
            return new ol.layer.Vector({
                source: new ol.source.Vector(),
                name: layerName,
                zIndex: zIndex,
                visible: true
            });
        }
    }

    // Function to create layer from GeoJSON data with EPSG:32650 (UTM Zone 50N)
    function createLayerFromGeoJson32650(geoJsonData, layerName, styleFunction, zIndex = 300) {
        if (!geoJsonData) {
            console.warn(`${layerName}: GeoJSON data is null or undefined`);
            return new ol.layer.Vector({
                source: new ol.source.Vector(),
                name: layerName,
                zIndex: zIndex,
                visible: true
            });
        }

        if (!geoJsonData.features || geoJsonData.features.length === 0) {
            console.warn(`${layerName}: GeoJSON data has no features (features: ${geoJsonData.features ? geoJsonData.features.length : 'null'})`);
            return new ol.layer.Vector({
                source: new ol.source.Vector(),
                name: layerName,
                zIndex: zIndex,
                visible: true
            });
        }

        try {
            console.log(`${layerName}: Parsing ${geoJsonData.features.length} features from EPSG:32650...`);
            
            // Register EPSG:32650 projection using proj4js
            if (typeof proj4 === 'undefined') {
                console.error(`${layerName}: proj4js library is required for EPSG:32650 transformation`);
                return new ol.layer.Vector({
                    source: new ol.source.Vector(),
                    name: layerName,
                    zIndex: zIndex,
                    visible: true
                });
            }
            
            // Define EPSG:32650 projection
            proj4.defs('EPSG:32650', '+proj=utm +zone=50 +datum=WGS84 +units=m +no_defs');
            
            // Register with OpenLayers
            if (typeof ol.proj.proj4 !== 'undefined') {
                ol.proj.proj4.register(proj4);
            }
            
            // Transform geometry coordinates recursively
            const transformGeometry = (geometry) => {
                if (!geometry || !geometry.coordinates) {
                    console.warn(`${layerName}: Geometry is null or has no coordinates`);
                    return geometry;
                }
                
                const transformCoordinate = (coord) => {
                    if (!Array.isArray(coord)) {
                        return coord;
                    }
                    
                    // Check if this is a coordinate pair [x, y] or [x, y, z]
                    if (coord.length >= 2 && 
                        typeof coord[0] === 'number' && 
                        typeof coord[1] === 'number' &&
                        (coord.length === 2 || (coord.length === 3 && typeof coord[2] === 'number'))) {
                        // This is a coordinate pair [x, y] or [x, y, z]
                        try {
                            // Transform from EPSG:32650 (UTM) to EPSG:4326 (WGS84)
                            const wgs84 = proj4('EPSG:32650', 'EPSG:4326', [coord[0], coord[1]]);
                            // Transform from EPSG:4326 to EPSG:3857 (Web Mercator)
                            const webMercator = ol.proj.transform(wgs84, 'EPSG:4326', 'EPSG:3857');
                            // Preserve z coordinate if present
                            if (coord.length === 3) {
                                return [webMercator[0], webMercator[1], coord[2]];
                            }
                            return webMercator;
                        } catch (e) {
                            console.warn(`${layerName}: Transform error for coord [${coord[0]}, ${coord[1]}]:`, e);
                            return coord;
                        }
                    } else {
                        // This is a nested array (ring or polygon), recurse
                        return coord.map(transformCoordinate);
                    }
                };
                
                try {
                    const transformedGeometry = JSON.parse(JSON.stringify(geometry));
                    transformedGeometry.coordinates = transformCoordinate(geometry.coordinates);
                    return transformedGeometry;
                } catch (e) {
                    console.error(`${layerName}: Error cloning geometry:`, e);
                    return geometry;
                }
            };
            
            // Transform all features
            const transformedFeatures = geoJsonData.features.map(feature => {
                const transformedFeature = {
                    type: 'Feature',
                    properties: feature.properties || {},
                    geometry: transformGeometry(feature.geometry)
                };
                
                // Read feature with transformed geometry (already in EPSG:3857)
                return new ol.format.GeoJSON().readFeature(transformedFeature, {
                    dataProjection: 'EPSG:3857',
                    featureProjection: 'EPSG:3857'
                });
            });

            console.log(`${layerName}: Successfully parsed ${transformedFeatures.length} features`);

            const source = new ol.source.Vector({
                features: transformedFeatures
            });
            
            return new ol.layer.Vector({
                source: source,
                style: styleFunction,
                name: layerName,
                zIndex: zIndex,
                visible: true,
                updateWhileAnimating: true, // Update style during animations
                updateWhileInteracting: true // Update style during interactions
            });
        } catch (error) {
            console.error(`${layerName}: Error parsing GeoJSON:`, error);
            console.error('Error details:', error.message);
            console.error('Error stack:', error.stack);
            return new ol.layer.Vector({
                source: new ol.source.Vector(),
                name: layerName,
                zIndex: zIndex,
                visible: true
            });
        }
    }

    // Style functions for different layers
    function getAreaKerjaStyle(feature) {
        // Use risk matrix calculation for boundary area kerja
        return getRiskBasedAreaKerjaStyle(feature);
    }

    // Calculate risk level for area kerja based on risk matrix criteria
    // Versi dengan parameter cctvList untuk menghindari duplicate API call
    function calculateRiskForAreaKerjaWithCctvList(feature, cctvList, hasOnlineCctv) {
        const props = feature.getProperties();
        const lokasiName = props.lokasi || props.nama_lokasi || props.name || '';
        const idLokasi = props.id_lokasi || props.id || '';
        
        // Criteria 1: Terdapat Laporan SAP dari SO PJA CCTV (minimal 1 OIH)
        // Konsep: dihitung ada laporan jika lokasi SAP dan lokasi boundary area kerja ada di hari itu
        const hasSapReportFromPja = hasSapReportToday('area_kerja', idLokasi, lokasiName, null, null, feature.getGeometry());
        
        // Criteria 2: CCTV Kondisi Online (Critical) - menggunakan parameter yang sudah dihitung
        // hasOnlineCctv sudah dihitung dari cctvList yang diambil dari API
        
        // Criteria 3: Area Highrisk ada Laporan SAP (Critical)
        // Check if this is a high-risk area and has SAP report
        const isHighRiskArea = checkIfHighRiskArea(lokasiName, idLokasi);
        const hasSapInHighRiskArea = isHighRiskArea && hasSapReportFromPja;
        
        // Determine risk level based on risk matrix
        // HIGH (Red):
        // - Semua kondisi TIDAK MEMENUHI
        // - Hanya "Terdapat Laporan SAP dari SO PJA CCTV" MEMENUHI
        // - Hanya "Area Highrisk ada Laporan SAP (Critical)" MEMENUHI
        if (!hasSapReportFromPja && !hasOnlineCctv && !hasSapInHighRiskArea) {
            return 'HIGH'; // Semua TIDAK MEMENUHI
        }
        if (hasSapReportFromPja && !hasOnlineCctv && !hasSapInHighRiskArea) {
            return 'HIGH'; // Hanya SAP MEMENUHI
        }
        if (!hasSapReportFromPja && !hasOnlineCctv && hasSapInHighRiskArea) {
            return 'HIGH'; // Hanya High Risk SAP MEMENUHI
        }
        
        // MEDIUM (Yellow):
        // - "Terdapat Laporan SAP dari SO PJA CCTV" TIDAK MEMENUHI, tapi "Area Highrisk ada Laporan SAP (Critical)" dan "CCTV Kondisi Online (Critical)" MEMENUHI
        // - "Terdapat Laporan SAP dari SO PJA CCTV" MEMENUHI, "Area Highrisk ada Laporan SAP (Critical)" TIDAK MEMENUHI, tapi "CCTV Kondisi Online (Critical)" MEMENUHI
        if (!hasSapReportFromPja && hasSapInHighRiskArea && hasOnlineCctv) {
            return 'MEDIUM';
        }
        if (hasSapReportFromPja && !hasSapInHighRiskArea && hasOnlineCctv) {
            return 'MEDIUM';
        }
        
        // NORMAL (Green):
        // - Semua kondisi MEMENUHI
        if (hasSapReportFromPja && hasOnlineCctv && (hasSapInHighRiskArea || !isHighRiskArea)) {
            return 'NORMAL';
        }
        
        // Default to MEDIUM if conditions don't match exactly
        return 'MEDIUM';
    }

    // Calculate risk level for area kerja based on risk matrix criteria
    // Sekarang async karena checkCctvOnlineInArea menjadi async
    async function calculateRiskForAreaKerja(feature) {
        const props = feature.getProperties();
        const lokasiName = props.lokasi || props.nama_lokasi || props.name || '';
        const idLokasi = props.id_lokasi || props.id || '';
        
        // Criteria 1: Terdapat Laporan SAP dari SO PJA CCTV (minimal 1 OIH)
        // Konsep: dihitung ada laporan jika lokasi SAP dan lokasi boundary area kerja ada di hari itu
        const hasSapReportFromPja = hasSapReportToday('area_kerja', idLokasi, lokasiName, null, null, feature.getGeometry());
        
        // Criteria 2: CCTV Kondisi Online (Critical)
        // Check if there are online CCTV in this area (async)
        const hasOnlineCctv = await checkCctvOnlineInArea(lokasiName, idLokasi, feature.getGeometry());
        
        // Criteria 3: Area Highrisk ada Laporan SAP (Critical)
        // Check if this is a high-risk area and has SAP report
        const isHighRiskArea = checkIfHighRiskArea(lokasiName, idLokasi);
        const hasSapInHighRiskArea = isHighRiskArea && hasSapReportFromPja;
        
        // Determine risk level based on risk matrix
        // HIGH (Red):
        // - Semua kondisi TIDAK MEMENUHI
        // - Hanya "Terdapat Laporan SAP dari SO PJA CCTV" MEMENUHI
        // - Hanya "Area Highrisk ada Laporan SAP (Critical)" MEMENUHI
        if (!hasSapReportFromPja && !hasOnlineCctv && !hasSapInHighRiskArea) {
            return 'HIGH'; // Semua TIDAK MEMENUHI
        }
        if (hasSapReportFromPja && !hasOnlineCctv && !hasSapInHighRiskArea) {
            return 'HIGH'; // Hanya SAP MEMENUHI
        }
        if (!hasSapReportFromPja && !hasOnlineCctv && hasSapInHighRiskArea) {
            return 'HIGH'; // Hanya High Risk SAP MEMENUHI
        }
        
        // MEDIUM (Yellow):
        // - "Terdapat Laporan SAP dari SO PJA CCTV" TIDAK MEMENUHI, tapi "Area Highrisk ada Laporan SAP (Critical)" dan "CCTV Kondisi Online (Critical)" MEMENUHI
        // - "Terdapat Laporan SAP dari SO PJA CCTV" MEMENUHI, "Area Highrisk ada Laporan SAP (Critical)" TIDAK MEMENUHI, tapi "CCTV Kondisi Online (Critical)" MEMENUHI
        if (!hasSapReportFromPja && hasSapInHighRiskArea && hasOnlineCctv) {
            return 'MEDIUM';
        }
        if (hasSapReportFromPja && !hasSapInHighRiskArea && hasOnlineCctv) {
            return 'MEDIUM';
        }
        
        // NORMAL (Green):
        // - Semua kondisi MEMENUHI
        if (hasSapReportFromPja && hasOnlineCctv && (hasSapInHighRiskArea || !isHighRiskArea)) {
            return 'NORMAL';
        }
        
        // Default to MEDIUM if conditions don't match exactly
        return 'MEDIUM';
    }

    // Check if CCTV is online in the area
    // Menggunakan data dari API (cctv_coverage) untuk akurasi yang lebih baik
    async function checkCctvOnlineInArea(lokasiName, idLokasi, geometry) {
        // Gunakan getCctvInArea yang sudah mengambil dari API
        const cctvInArea = await getCctvInArea(lokasiName, idLokasi, geometry);
        
        if (cctvInArea.length === 0) {
            console.log(`[Risk Matrix] No CCTV found in area: ${lokasiName}`);
            return false;
        }
        
        // Check if at least one CCTV is online
        // CCTV dianggap online jika: kondisi = 'Baik' atau 'Online', status = 'Live View', connected = 'Yes'
        const hasOnline = cctvInArea.some(cctv => {
            const kondisi = (cctv.kondisi || '').toLowerCase();
            const status = (cctv.status || '').toLowerCase();
            const connected = (cctv.connected || '').toLowerCase();
            
            // Check various online indicators
            const isOnline = 
                kondisi === 'baik' || 
                kondisi === 'online' || 
                status === 'live view' || 
                status === 'online' ||
                status === 'baik' ||
                connected === 'yes' || 
                connected === 'true' ||
                cctv.status === 1 || 
                cctv.is_online === true || 
                cctv.status_online === 1 ||
                (cctv.kondisi && cctv.kondisi.toString().toLowerCase() === 'baik') ||
                (cctv.status && cctv.status.toString().toLowerCase() === 'live view');
            
            if (isOnline) {
                console.log(`[Risk Matrix] CCTV Online found: ${cctv.nama_cctv || cctv.no_cctv} - kondisi: ${kondisi}, status: ${status}, connected: ${connected}`);
            }
            
            return isOnline;
        });
        
        console.log(`[Risk Matrix] Area: ${lokasiName}, CCTV in area: ${cctvInArea.length}, Has online: ${hasOnline}`);
        return hasOnline;
    }

    // Check if area is high-risk
    function checkIfHighRiskArea(lokasiName, idLokasi) {
        // Define high-risk area keywords
        const highRiskKeywords = ['pit', 'hauling', 'tambang', 'mining', 'high risk', 'highrisk'];
        const areaName = lokasiName.toLowerCase().trim();
        
        // Check if area name contains high-risk keywords
        return highRiskKeywords.some(keyword => areaName.includes(keyword));
    }

    // Get risk-based style for area kerja
    // Pulse animation state untuk boundary
    let pulseAnimationStartTime = null;
    let pulseAnimationRunning = false;
    
    // Start pulse animation loop - Infinity loop yang tidak pernah berhenti
    function startPulseAnimation() {
        if (pulseAnimationRunning) return;
        pulseAnimationRunning = true;
        pulseAnimationStartTime = performance.now();
        
        function animate() {
            // Pastikan animasi selalu berjalan
            if (!pulseAnimationRunning) {
                pulseAnimationRunning = true;
            }
            
            // Get all area kerja layers dan force update
            map.getLayers().forEach(layer => {
                const layerName = layer.get('name') || '';
                if (layerName && layerName.includes('Area Kerja')) {
                    const source = layer.getSource();
                    if (source) {
                        // Trigger changed event untuk memaksa re-render dengan style baru
                        // Ini akan memanggil ulang style function untuk semua features
                        source.changed();
                    }
                }
            });
            
            // Update insiden layer untuk pulsating animation
            if (insidenLayer) {
                const source = insidenLayer.getSource();
                if (source) {
                    source.changed();
                }
            }
            
            // Update CCTV layer untuk blink animation (CCTV yang rusak akan blink merah)
            if (cctvLayer) {
                const source = cctvLayer.getSource();
                if (source) {
                    source.changed();
                }
            }
            
            // Update DOP layer untuk blink animation (card DOP akan blink merah)
            if (dailyOperationPlansLayer) {
                const source = dailyOperationPlansLayer.getSource();
                if (source) {
                    source.changed();
                }
            }
            
            // Trigger map re-render untuk update style
            if (map) {
                map.render();
            }
            
            // Continue infinite loop
            requestAnimationFrame(animate);
        }
        
        // Start infinite loop
        requestAnimationFrame(animate);
    }
    
    // Get current animation time - Selalu return waktu yang valid untuk infinite loop
    function getPulseAnimationTime() {
        if (!pulseAnimationStartTime) {
            // Jika belum diinisialisasi, inisialisasi sekarang
            pulseAnimationStartTime = performance.now();
            return 0;
        }
        // Gunakan modulo untuk mencegah overflow dan memastikan infinite loop
        const elapsed = performance.now() - pulseAnimationStartTime;
        // Modulo dengan nilai besar untuk mencegah overflow (100 jam)
        return elapsed % (100 * 60 * 60 * 1000);
    }
    
    // Ease-out easing function (mirip dengan CSS ease-out)
    function easeOut(t) {
        return 1 - Math.pow(1 - t, 3);
    }
    
    // Calculate pulse values based on time - Infinite loop seperti CSS @keyframes pulseStrokeFast/Slow
    // Menggunakan modulo untuk memastikan infinite loop yang tidak pernah berhenti
    function getPulseValues(time, pulseSpeed = 'fast') {
        // Fast: 1.0s cycle (semua sekarang fast)
        const cycle = 1000; // Semua menggunakan cycle 1.0s
        // Modulo memastikan progress selalu 0-1, membuat infinite loop
        const progress = (time % cycle) / cycle;
        
        let strokeWidth, strokeOpacity;
        
        // pulseStrokeFast: 0% → 60% → 100% (sesuai CSS keyframes)
        // 0%: stroke-width: 2.5, stroke-opacity: 1
        // 60%: stroke-width: 8, stroke-opacity: 0.18
        // 100%: stroke-width: 2.5, stroke-opacity: 1
        if (progress <= 0.6) {
            // Expanding phase (0-60%)
            const phaseProgress = progress / 0.6;
            const eased = easeOut(phaseProgress);
            strokeWidth = 2.5 + (8 - 2.5) * eased;
            strokeOpacity = 1 - (1 - 0.18) * eased;
        } else {
            // Contracting phase (60-100%)
            const phaseProgress = (progress - 0.6) / 0.4;
            const eased = easeOut(phaseProgress);
            strokeWidth = 8 - (8 - 2.5) * eased;
            strokeOpacity = 0.18 + (1 - 0.18) * eased;
        }
        
        return { strokeWidth, strokeOpacity };
    }
    
    // Calculate pulsating circle values for insiden markers
    // Based on CSS animation: pulse-ring and pulse-dot
    function getPulsatingCircleValues(time) {
        const cycle = 1250; // 1.25s cycle (matching CSS animation)
        const progress = (time % cycle) / cycle;
        
        // Pulse ring animation (outer expanding circle)
        // 0%: scale 0.33, opacity 1
        // 80-100%: opacity 0
        let ringScale, ringOpacity;
        if (progress <= 0.8) {
            // Expanding phase (0-80%)
            const phaseProgress = progress / 0.8;
            // Easing: cubic-bezier(0.215, 0.61, 0.355, 1)
            const eased = 1 - Math.pow(1 - phaseProgress, 3);
            ringScale = 0.33 + (3.0 - 0.33) * eased; // Scale from 0.33 to 3.0 (300%)
            ringOpacity = 1 - (progress / 0.8) * 1; // Fade from 1 to 0
        } else {
            // Fade out phase (80-100%)
            ringScale = 3.0;
            ringOpacity = 0;
        }
        
        // Pulse dot animation (inner dot)
        // 0%: scale 0.8
        // 50%: scale 1.0
        // 100%: scale 0.8
        // Easing: cubic-bezier(0.455, 0.03, 0.515, 0.955) with -0.4s delay
        let dotScale;
        const dotProgress = (progress + 0.4) % 1.0; // Add delay
        if (dotProgress <= 0.5) {
            // Expanding (0-50%)
            const phaseProgress = dotProgress / 0.5;
            dotScale = 0.8 + (1.0 - 0.8) * phaseProgress;
        } else {
            // Contracting (50-100%)
            const phaseProgress = (dotProgress - 0.5) / 0.5;
            dotScale = 1.0 - (1.0 - 0.8) * phaseProgress;
        }
        
        return { ringScale, ringOpacity, dotScale };
    }
    
    // Style akan menggunakan cached risk level jika sudah dihitung
    // Jika belum, akan menggunakan default MEDIUM (kuning)
    function getRiskBasedAreaKerjaStyle(feature) {
        // Cek apakah feature sudah punya cached risk level
        // Risk level di-set saat popup dibuka dan risk matrix summary dihitung
        const cachedRiskLevel = feature.get('riskLevel');
        
        let fillColor, strokeColor, pulseSpeed;
        
        if (cachedRiskLevel) {
            console.log(`[getRiskBasedAreaKerjaStyle] Using cached risk level: ${cachedRiskLevel} for feature: ${feature.get('lokasi') || feature.getId()}`);
            switch(cachedRiskLevel) {
                case 'HIGH':
                    fillColor = 'rgba(217, 45, 32, 0.22)'; // Red #d92d20 dengan opacity 0.22
                    strokeColor = '#d92d20'; // Red sesuai contoh
                    pulseSpeed = 'fast'; // Pulse cepat untuk HIGH risk
                    break;
                case 'MEDIUM':
                    fillColor = 'rgba(247, 144, 9, 0.22)'; // Orange #f79009 dengan opacity 0.22
                    strokeColor = '#f79009'; // Orange sesuai contoh
                    pulseSpeed = 'fast'; // Pulse cepat untuk MEDIUM risk
                    break;
                case 'NORMAL':
                default:
                    fillColor = 'rgba(18, 183, 106, 0.18)'; // Green #12b76a dengan opacity 0.18
                    strokeColor = '#12b76a'; // Green sesuai contoh
                    pulseSpeed = null; // Tidak ada pulse untuk NORMAL
                    break;
            }
        } else {
            // Default MEDIUM (orange) jika belum dihitung
            fillColor = 'rgba(247, 144, 9, 0.22)'; // Orange (default) dengan opacity 0.22
            strokeColor = '#f79009';
            pulseSpeed = 'fast';
        }
        
        // Apply pulse effect untuk HIGH dan MEDIUM risk - Infinite loop blink
        let strokeWidth = 2.5;
        let strokeOpacity = 1;
        
        // Semua boundary (HIGH, MEDIUM, dan default) akan blink terus menerus
        if (pulseSpeed && (cachedRiskLevel === 'HIGH' || cachedRiskLevel === 'MEDIUM' || !cachedRiskLevel)) {
            const pulse = getPulseValues(getPulseAnimationTime(), pulseSpeed);
            strokeWidth = pulse.strokeWidth;
            strokeOpacity = pulse.strokeOpacity;
        }
        
        // Convert stroke color to rgba dengan opacity untuk pulse effect
        let strokeColorWithOpacity = strokeColor;
        if (strokeOpacity < 1) {
            // Convert hex to rgba
            const hex = strokeColor.replace('#', '');
            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);
            strokeColorWithOpacity = `rgba(${r}, ${g}, ${b}, ${strokeOpacity})`;
        }
        
        // Selalu buat style baru untuk memastikan animasi berjalan
        // OpenLayers akan memanggil ulang style function jika style object berbeda
        // Dengan membuat style object baru setiap kali, OpenLayers akan mengenali perubahan
        return new ol.style.Style({
            fill: new ol.style.Fill({ color: fillColor }),
            stroke: new ol.style.Stroke({ 
                color: strokeColorWithOpacity, 
                width: strokeWidth,
                lineCap: 'round',
                lineJoin: 'round'
            }),
            zIndex: strokeOpacity < 1 ? 1 : 0 // Slight z-index adjustment untuk layering
        });
    }

    // Get CCTV list in area
    // Menggunakan API untuk mengambil CCTV dari tabel cctv_coverage berdasarkan coverage_lokasi
    // Kemudian join dengan cctv_data_bmo2 untuk mendapatkan data lengkap
    async function getCctvInArea(lokasiName, idLokasi, geometry) {
        if (!lokasiName) {
            console.log('[getCctvInArea] No location name provided');
            return [];
        }
        
        console.log(`[getCctvInArea] Searching for area: "${lokasiName}"`);
        
        try {
            // Ambil CCTV dari API berdasarkan coverage_lokasi
            const response = await fetch(`{{ route('full-maps.api.cctv-by-coverage') }}?lokasi_name=${encodeURIComponent(lokasiName)}`);
            const result = await response.json();
            
            if (result.success && result.data && result.data.length > 0) {
                console.log(`[getCctvInArea] Found ${result.data.length} CCTV(s) from API for area "${lokasiName}"`);
                return result.data;
            } else {
                console.log(`[getCctvInArea] No CCTV found from API for area "${lokasiName}"`);
                
                // Fallback: coba dengan data CCTV yang sudah ada di cctvLocations
                if (cctvLocations && cctvLocations.length > 0) {
                    const matchedCctv = cctvLocations.filter(cctv => {
                        let matched = false;
                        
                        // Prioritas 1: Check coverage_lokasi dengan flexible matching
                        if (cctv.coverage_lokasi) {
                            if (simpleLocationMatch(lokasiName, cctv.coverage_lokasi) || 
                                isLocationMatch(lokasiName, cctv.coverage_lokasi)) {
                                matched = true;
                            }
                        }
                        
                        // Prioritas 2: Check coverage_detail_lokasi dengan flexible matching
                        if (!matched && cctv.coverage_detail_lokasi) {
                            if (simpleLocationMatch(lokasiName, cctv.coverage_detail_lokasi) || 
                                isLocationMatch(lokasiName, cctv.coverage_detail_lokasi)) {
                                matched = true;
                            }
                        }
                        
                        // Prioritas 3: Check lokasi_pemasangan dengan flexible matching
                        if (!matched && cctv.lokasi_pemasangan) {
                            if (simpleLocationMatch(lokasiName, cctv.lokasi_pemasangan) || 
                                isLocationMatch(lokasiName, cctv.lokasi_pemasangan)) {
                                matched = true;
                            }
                        }
                        
                        // Check coordinate matching (jika geometry tersedia) - sebagai fallback
                        if (!matched && geometry) {
                            if (cctv.location && Array.isArray(cctv.location) && cctv.location.length >= 2) {
                                try {
                                    const lng = parseFloat(cctv.location[0]);
                                    const lat = parseFloat(cctv.location[1]);
                                    if (!isNaN(lat) && !isNaN(lng)) {
                                        const cctvCoord = ol.proj.fromLonLat([lng, lat]);
                                        if (geometry.intersectsCoordinate(cctvCoord)) {
                                            matched = true;
                                        }
                                    }
                                } catch (e) {
                                    // Silently fail
                                }
                            }
                            
                            if (!matched && cctv.latitude && cctv.longitude) {
                                try {
                                    const lat = parseFloat(cctv.latitude);
                                    const lng = parseFloat(cctv.longitude);
                                    if (!isNaN(lat) && !isNaN(lng)) {
                                        const cctvCoord = ol.proj.fromLonLat([lng, lat]);
                                        if (geometry.intersectsCoordinate(cctvCoord)) {
                                            matched = true;
                                        }
                                    }
                                } catch (e) {
                                    // Silently fail
                                }
                            }
                        }
                        
                        return matched;
                    });
                    
                    console.log(`[getCctvInArea] Found ${matchedCctv.length} CCTV(s) from fallback for area "${lokasiName}"`);
                    return matchedCctv;
                }
            }
            
            return [];
        } catch (error) {
            console.error('[getCctvInArea] Error fetching CCTV from API:', error);
            return [];
        }
    }

    // Get SAP reports in area for today
    function getSapReportsInArea(lokasiName, idLokasi, geometry) {
        if (!sapDataForSidebar || sapDataForSidebar.length === 0) {
            return [];
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const todayStr = today.toISOString().split('T')[0];
        
        // Filter SAP data for today
        const sapToday = sapDataForSidebar.filter(sap => {
            if (!sap.tanggal_pelaporan && !sap.detected_at) return false;
            try {
                const sapDate = new Date(sap.tanggal_pelaporan || sap.detected_at);
                sapDate.setHours(0, 0, 0, 0);
                const sapDateStr = sapDate.toISOString().split('T')[0];
                return sapDateStr === todayStr;
            } catch (e) {
                return false;
            }
        });
        
        if (sapToday.length === 0) {
            return [];
        }
        
        // Normalize area location name
        const normalizedAreaName = normalizeLocationName(lokasiName || '');
        
        // Filter SAP that matches the area
        return sapToday.filter(sap => {
            // Check coordinate
            if (geometry) {
                const sapLat = sap.latitude || (sap.location && sap.location.lat) || null;
                const sapLng = sap.longitude || (sap.location && sap.location.lng) || null;
                
                if (sapLat && sapLng && !isNaN(parseFloat(sapLat)) && !isNaN(parseFloat(sapLng))) {
                    if (isCoordinateInGeometry(parseFloat(sapLat), parseFloat(sapLng), geometry)) {
                        return true;
                    }
                }
            }
            
            // Check location name
            if (normalizedAreaName) {
                const sapLokasi = normalizeLocationName(sap.lokasi || '');
                const sapDetailLokasi = normalizeLocationName(sap.detail_lokasi || '');
                
                const nameMatch = (
                    (sapLokasi && (sapLokasi.includes(normalizedAreaName) || normalizedAreaName.includes(sapLokasi))) ||
                    (sapDetailLokasi && (sapDetailLokasi.includes(normalizedAreaName) || normalizedAreaName.includes(sapDetailLokasi)))
                );
                
                if (nameMatch) return true;
            }
            
            return false;
        });
    }

    // Get detailed risk matrix summary for popup display
    async function getRiskMatrixSummary(feature) {
        const props = feature.getProperties();
        const lokasiName = props.lokasi || props.nama_lokasi || props.name || '';
        const idLokasi = props.id_lokasi || props.id || '';
        const geometry = feature.getGeometry();
        
        // Get CCTV list in area (async) - ambil dari API
        const cctvList = await getCctvInArea(lokasiName, idLokasi, geometry);
        const cctvCount = cctvList.length;
        
        // Check if at least one CCTV is online dari data yang sudah diambil
        // Gunakan data dari API untuk akurasi yang lebih baik
        // Logika HARUS SAMA PERSIS dengan yang digunakan di popup (line 5151-5154) untuk konsistensi
        console.log(`[getRiskMatrixSummary] Checking online status for ${cctvList.length} CCTV(s)`);
        
        const hasOnlineCctv = cctvList.some(cctv => {
            // Gunakan logika yang SAMA PERSIS dengan popup (line 5149-5154)
            const kondisi = cctv.kondisi || cctv.status || 'Unknown';
            const kondisiLower = (kondisi || '').toLowerCase();
            
            // Logika SAMA PERSIS dengan popup
            const isOnline = 
                kondisiLower === 'baik' || 
                kondisiLower === 'online' || 
                (cctv.status || '').toLowerCase() === 'live view' || 
                (cctv.connected || '').toLowerCase() === 'yes' ||
                cctv.status === 1 || 
                cctv.is_online === true || 
                cctv.status_online === 1;
            
            if (isOnline) {
                console.log(`[getRiskMatrixSummary] ✓ CCTV Online found: ${cctv.nama_cctv || cctv.no_cctv || cctv.id} - kondisi: "${kondisi}", status: "${cctv.status}", connected: "${cctv.connected}"`);
            } else {
                console.log(`[getRiskMatrixSummary] ✗ CCTV Offline: ${cctv.nama_cctv || cctv.no_cctv || cctv.id} - kondisi: "${kondisi}", status: "${cctv.status}", connected: "${cctv.connected}"`);
            }
            
            return isOnline;
        });
        
        console.log(`[getRiskMatrixSummary] Has Online CCTV: ${hasOnlineCctv} (out of ${cctvList.length} CCTV)`);
        
        // Calculate all criteria
        const hasSapReportFromPja = hasSapReportToday('area_kerja', idLokasi, lokasiName, null, null, geometry);
        const isHighRiskArea = checkIfHighRiskArea(lokasiName, idLokasi);
        const hasSapInHighRiskArea = isHighRiskArea && hasSapReportFromPja;
        
        // Get SAP reports in area
        const sapReports = getSapReportsInArea(lokasiName, idLokasi, geometry);
        
        // Calculate risk level (async) - pass cctvList to avoid duplicate API call
        const riskLevel = await calculateRiskForAreaKerjaWithCctvList(feature, cctvList, hasOnlineCctv);
        
        // Determine risk color
        let riskColor;
        switch(riskLevel) {
            case 'HIGH':
                riskColor = '#dc2626'; // Red
                break;
            case 'MEDIUM':
                riskColor = '#facc15'; // Yellow
                break;
            case 'NORMAL':
            default:
                riskColor = '#22c55e'; // Green
                break;
        }
        
        console.log(`[getRiskMatrixSummary] Area: ${lokasiName}, CCTV Count: ${cctvCount}, Has Online: ${hasOnlineCctv}, Risk Level: ${riskLevel}`);
        
        // Simpan risk level ke feature untuk digunakan oleh styling
        feature.set('riskLevel', riskLevel);
        
        // Update style feature setelah risk level dihitung
        // Ini akan memperbarui warna boundary area kerja secara real-time
        const newStyle = getRiskBasedAreaKerjaStyle(feature);
        feature.setStyle(newStyle);
        
        // Juga update style di layer jika layer menggunakan style function
        // Cari layer yang mengandung feature ini dan update style-nya
        const source = feature.get('source') || feature.get('layer')?.getSource();
        if (source) {
            // Trigger style recalculation untuk feature ini
            source.changed();
        }
        
        // Trigger map render untuk memperbarui tampilan
        // Gunakan requestAnimationFrame untuk update yang lebih smooth
        requestAnimationFrame(() => {
            map.render();
        });
        
        return {
            riskLevel: riskLevel,
            riskColor: riskColor,
            hasSapReport: hasSapReportFromPja,
            hasOnlineCctv: hasOnlineCctv,
            isHighRiskArea: isHighRiskArea,
            hasSapInHighRiskArea: hasSapInHighRiskArea,
            cctvCount: cctvCount,
            cctvList: cctvList,
            sapReports: sapReports
        };
    }

    function getAreaCctvStyle(feature) {
        return new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(139, 92, 246, 0.3)' // Purple with transparency
            }),
            stroke: new ol.style.Stroke({
                color: '#8b5cf6', // Purple
                width: 2
            })
        });
    }

    function getDifferenceStyle(feature) {
        return new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(239, 68, 68, 0.4)' // Red with transparency
            }),
            stroke: new ol.style.Stroke({
                color: '#ef4444', // Red
                width: 2
            })
        });
    }

    function getSymmetricalDifferenceStyle(feature) {
        return new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(245, 158, 11, 0.4)' // Orange with transparency
            }),
            stroke: new ol.style.Stroke({
                color: '#f59e0b', // Orange
                width: 2
            })
        });
    }

    function getIntersectionStyle(feature) {
        return new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(34, 197, 94, 0.4)' // Green with transparency
            }),
            stroke: new ol.style.Stroke({
                color: '#22c55e', // Green
                width: 2
            })
        });
    }

    // Function to create WMS layer
    function createWMSLayer(layerName = '', serverKey = currentWmsServer) {
        const server = wmsServers[serverKey];
        const params = {
            'LAYERS': layerName || '0',
            'VERSION': '1.1.1',
            'FORMAT': 'image/png',
            'TRANSPARENT': true,
            'TILED': true
        };
        
        return new ol.layer.Tile({
            source: new ol.source.TileWMS({
                url: server.url,
                params: params,
                serverType: 'mapserver',
                crossOrigin: 'anonymous',
                tileGrid: new ol.tilegrid.TileGrid({
                    extent: ol.proj.transformExtent(
                        server.bbox,
                        'EPSG:4326',
                        'EPSG:3857'
                    ),
                    resolutions: [
                        156543.03392804097,
                        78271.51696402048,
                        39135.75848201024,
                        19567.87924100512,
                        9783.93962050256,
                        4891.96981025128,
                        2445.98490512564,
                        1222.99245256282,
                        611.49622628141,
                        305.748113140705,
                        152.8740565703525,
                        76.43702828517625,
                        38.21851414258813,
                        19.109257071294063,
                        9.554628535647032,
                        4.777314267823516,
                        2.388657133911758,
                        1.194328566955879,
                        0.5971642834779395
                    ],
                    tileSize: [256, 256]
                })
            }),
            zIndex: 1,
            opacity: 0.85
        });
    }

    // Create map dengan Google Satellite sebagai base layer
    const map = new ol.Map({
        target: 'hazardMap',
        layers: [
            // Base layer - Google Satellite (fallback)
            new ol.layer.Tile({
                source: googleSatelliteSource,
                opacity: 1.0
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat(wmsServers[currentWmsServer].center),
            zoom: 15
        }),
        controls: [
            new ol.control.Zoom(),
            new ol.control.ScaleLine(),
            new ol.control.MousePosition({
                coordinateFormat: function(coordinate) {
                    if (coordinate) {
                        return coordinate[0].toFixed(4) + ', ' + coordinate[1].toFixed(4);
                    }
                    return '';
                },
                projection: 'EPSG:4326'
            })
        ]
    });

    // Make map view globally accessible for Google Maps style controls
    window.hazardMapView = map.getView();
    
    // Start pulse animation untuk boundary area kerja
    startPulseAnimation();

    // Map Layer Filter - Store base layer reference and create different tile sources
    let baseLayer = map.getLayers().item(0); // Get the first layer (base layer)
    let currentMapType = 'satellite'; // Default map type
    
    // Create different tile sources for map types
    // const mapTileSources = {
    //     satellite: googleSatelliteSource, // Already defined
    //     terrain: new ol.source.XYZ({
    //         url: 'http://mt0.google.com/vt/lyrs=p&hl=en&x={x}&y={y}&z={z}',
    //         attributions: '© Google',
    //         maxZoom: 20
    //     }),
    //     road: new ol.source.XYZ({
    //         url: 'http://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
    //         attributions: '© Google',
    //         maxZoom: 20
    //     }),
    //     traffic: new ol.source.XYZ({
    //         url: 'http://mt0.google.com/vt/lyrs=m@221097413,traffic&hl=en&x={x}&y={y}&z={z}',
    //         attributions: '© Google',
    //         maxZoom: 20
    //     }),
    //     transit: new ol.source.XYZ({
    //         url: 'http://mt0.google.com/vt/lyrs=m@221097413,transit&hl=en&x={x}&y={y}&z={z}',
    //         attributions: '© Google',
    //         maxZoom: 20
    //     }),
    //     biking: new ol.source.XYZ({
    //         url: 'http://mt0.google.com/vt/lyrs=m@221097413,bike&hl=en&x={x}&y={y}&z={z}',
    //         attributions: '© Google',
    //         maxZoom: 20
    //     })
    // };

    // === NON-GOOGLE TILE SOURCES (OpenLayers) ===
    // Tips: pakai HTTPS + set crossOrigin untuk aman.

    const mapTileSources = {
    // --- SATELLITE ---
    // Esri World Imagery (satellite)
    satellite_esri: new ol.source.XYZ({
        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        attributions: 'Tiles © Esri',
        maxZoom: 19,
        crossOrigin: 'anonymous'
    }),

    // --- ROAD / BASIC ---
    // OpenStreetMap Standard (road)
    road_osm: new ol.source.XYZ({
        url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        attributions: '© OpenStreetMap contributors',
        maxZoom: 19,
        crossOrigin: 'anonymous'
    }),

    // CARTO Voyager (road-ish, lebih “rapi” untuk overlay data)
    road_carto_voyager: new ol.source.XYZ({
        url: 'https://tiles.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png',
        attributions: '© OpenStreetMap contributors, © CARTO',
        maxZoom: 20,
        crossOrigin: 'anonymous'
    }),

    // --- TERRAIN / TOPO ---
    // OpenTopoMap (topo/terrain)
    terrain_opentopo: new ol.source.XYZ({
        url: 'https://tile.opentopomap.org/{z}/{x}/{y}.png',
        attributions: '© OpenStreetMap contributors, © OpenTopoMap',
        maxZoom: 17,
        crossOrigin: 'anonymous'
    }),

    // Esri World Topo Map (topo)
    topo_esri: new ol.source.XYZ({
        url: 'https://services.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
        attributions: 'Tiles © Esri',
        maxZoom: 19,
        crossOrigin: 'anonymous'
    }),

    // --- LIGHT / DARK (bagus untuk dashboard) ---
    // CARTO Positron (light)
    light_carto: new ol.source.XYZ({
        url: 'https://{a-d}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png',
        attributions: '© OpenStreetMap contributors, © CARTO',
        maxZoom: 20,
        crossOrigin: 'anonymous'
    }),

    // CARTO Dark Matter (dark)
    dark_carto: new ol.source.XYZ({
        url: 'https://{a-d}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png',
        attributions: '© OpenStreetMap contributors, © CARTO',
        maxZoom: 20,
        crossOrigin: 'anonymous'
    }),

    // --- “SEGALAMACAM” (butuh key) ---
    // Stadia (hosting style Stamen) - butuh api_key
    // Contoh: Toner (high contrast)
    toner_stadia: new ol.source.XYZ({
        url: 'https://tiles.stadiamaps.com/tiles/stamen_toner/{z}/{x}/{y}.png?api_key=YOUR_STADIA_KEY',
        attributions: '© Stadia Maps © Stamen Design © OpenMapTiles © OpenStreetMap contributors',
        maxZoom: 20,
        crossOrigin: 'anonymous'
    }),

    // Stadia: Terrain (lebih “terrain” feel)
    terrain_stadia: new ol.source.XYZ({
        url: 'https://tiles.stadiamaps.com/tiles/stamen_terrain/{z}/{x}/{y}.png?api_key=YOUR_STADIA_KEY',
        attributions: '© Stadia Maps © Stamen Design © OpenMapTiles © OpenStreetMap contributors',
        maxZoom: 20,
        crossOrigin: 'anonymous'
    }),

    // Thunderforest: Outdoors/Landscape/Cycle/Transport (butuh apikey)
    outdoors_thunderforest: new ol.source.XYZ({
        url: 'https://{a-c}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png?apikey=YOUR_THUNDERFOREST_KEY',
        attributions: '© Thunderforest, © OpenStreetMap contributors',
        maxZoom: 22,
        crossOrigin: 'anonymous'
    })
    };


    // Function to switch map layer
    function switchMapLayer(layerType) {
        if (!mapTileSources[layerType]) {
            console.warn(`Map layer type "${layerType}" not supported`);
            return;
        }

        // Remove old base layer
        if (baseLayer) {
            map.removeLayer(baseLayer);
        }

        // Create new base layer
        baseLayer = new ol.layer.Tile({
            source: mapTileSources[layerType],
            opacity: 1.0
        });

        // Add new base layer at the bottom (zIndex 0)
        map.getLayers().insertAt(0, baseLayer);
        currentMapType = layerType;

        // Update active state in UI
        document.querySelectorAll('.gm-layer-option').forEach(option => {
            option.classList.remove('active');
            if (option.getAttribute('data-layer') === layerType) {
                option.classList.add('active');
            }
        });

        console.log(`Switched to ${layerType} map layer`);
    }

    // Layer Filter Toggle Functionality - New Design with Bootstrap Collapse
    function initLayerFilter() {
        const panelEl = document.getElementById('gmLayerPanel');
        const btn = document.getElementById('gmLayerToggleBtn');
        
        if (!panelEl || !btn) {
            console.warn('Layer filter elements not found');
            return;
        }

        // Bootstrap collapse instance
        const collapse = new bootstrap.Collapse(panelEl, { toggle: false });

        // Toggle show/hide panel
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isShown = panelEl.classList.contains('show');
            if (isShown) {
                collapse.hide();
                btn.setAttribute('aria-expanded', 'false');
            } else {
                collapse.show();
                btn.setAttribute('aria-expanded', 'true');
            }
        });

        // Prevent click inside panel from closing
        panelEl.addEventListener('click', (e) => e.stopPropagation());

        // Click outside to close
        document.addEventListener('click', () => {
            if (panelEl.classList.contains('show')) {
                collapse.hide();
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        // Toggle layer ON/OFF
        document.querySelectorAll('.btn-check').forEach(cb => {
            cb.addEventListener('change', () => {
                const label = document.querySelector(`label[for="${cb.id}"]`);
                const layerName = label?.dataset?.layer || cb.id;
                applyLayer(layerName, cb.checked);
            });
        });

        // Hook your map control here
        function applyLayer(layerName, isOn) {
            console.log('[Layer]', layerName, isOn ? 'ON' : 'OFF');
            
            // Special handling for "Unit dan Orang" (terrain layer)
            if (layerName === 'terrain') {
                // Hide CCTV first
                if (cctvLayer && layerVisibility.cctv) {
                    toggleLayerVisibility('cctv', false);
                }
                
                // Show Unit - use toggleUnitDisplay if available
                if (typeof toggleUnitDisplay === 'function') {
                    // Check if unit is already visible, if not show it
                    if (!layerVisibility.unit || !unitDataLoaded) {
                        toggleUnitDisplay();
                    } else {
                        // Ensure it's visible
                        toggleLayerVisibility('unit', true);
                    }
                } else {
                    toggleLayerVisibility('unit', true);
                }
                
                // Show GPS Orang - use toggleGpsOrangDisplay if available
                if (typeof toggleGpsOrangDisplay === 'function') {
                    // Check if GPS Orang is already visible, if not show it
                    if (!layerVisibility.gps || !gpsOrangDataLoaded) {
                        toggleGpsOrangDisplay();
                    } else {
                        // Ensure it's visible
                        toggleLayerVisibility('gps', true);
                    }
                } else {
                    // Fallback: find and click the GPS Orang category item
                    const gpsOrangCategoryItems = document.querySelectorAll('.gm-category-item');
                    let gpsOrangClicked = false;
                    gpsOrangCategoryItems.forEach(function(item) {
                        const span = item.querySelector('span');
                        if (span && (span.textContent.trim() === 'Gps Orang' || span.textContent.trim() === 'GPS Orang')) {
                            if (!layerVisibility.gps || !gpsOrangDataLoaded) {
                                item.click();
                                gpsOrangClicked = true;
                            } else {
                                toggleLayerVisibility('gps', true);
                            }
                        }
                    });
                    
                    // If GPS Orang category item not found, use direct toggle
                    if (!gpsOrangClicked) {
                        toggleLayerVisibility('gps', true);
                    }
                }
                
                console.log('Unit dan Orang layer activated - showing Unit and GPS Orang, hiding CCTV');
            } else if (layerName === 'satellite') {
                // Switch to satellite map
                switchMapLayer('satellite');
            } else if (layerName === 'traffic') {
                // Switch to traffic/matriks area kerja
                switchMapLayer('traffic');
                
                // Clear highlighted area kerja layer when toggling traffic layer
                if (highlightedAreaKerjaLayer) {
                    map.removeLayer(highlightedAreaKerjaLayer);
                    highlightedAreaKerjaLayer = null;
                }
                
                // Show/hide daily operation plans layer
                if (isOn) {
                    // Hide all area kerja layers from JS files (GeoJSON)
                    if (window.areaKerjaLayers && Array.isArray(window.areaKerjaLayers)) {
                        window.areaKerjaLayers.forEach(layer => {
                            if (layer) {
                                layer.setVisible(false);
                                console.log('Hiding area kerja layer from JS:', layer.get('name') || 'Unknown');
                            }
                        });
                    }
                    
                    // Hide areaKerjaBmo2PamaLayer if exists
                    if (areaKerjaBmo2PamaLayer) {
                        areaKerjaBmo2PamaLayer.setVisible(false);
                        console.log('Hiding Area Kerja BMO2 PAMA layer from JS');
                    }
                    
                    // Load and show daily operation plans (from ClickHouse)
                    if (dailyOperationPlansLayer) {
                        dailyOperationPlansLayer.setVisible(true);
                        loadDailyOperationPlans();
                        console.log('Showing daily operation plans layer (from ClickHouse)');
                        
                        // Update notification panel if it's open
                        setTimeout(() => {
                            const notificationPanel = document.getElementById('gmNotificationPanel');
                            if (notificationPanel && notificationPanel.classList.contains('active')) {
                                renderNotificationPanel();
                            }
                        }, 1000);
                    }
                } else {
                    // Hide daily operation plans
                    if (dailyOperationPlansLayer) {
                        dailyOperationPlansLayer.setVisible(false);
                        console.log('Hiding daily operation plans layer');
                        
                        // Update notification panel if it's open (switch back to risk matrix)
                        setTimeout(() => {
                            const notificationPanel = document.getElementById('gmNotificationPanel');
                            if (notificationPanel && notificationPanel.classList.contains('active')) {
                                renderNotificationPanel();
                            }
                        }, 300);
                    }
                    
                    // Show back all area kerja layers from JS files
                    if (window.areaKerjaLayers && Array.isArray(window.areaKerjaLayers)) {
                        window.areaKerjaLayers.forEach(layer => {
                            if (layer) {
                                layer.setVisible(true);
                                layer.setOpacity(1.0);
                                console.log('Showing area kerja layer from JS:', layer.get('name') || 'Unknown');
                            }
                        });
                    }
                    
                    // Show back areaKerjaBmo2PamaLayer if exists
                    if (areaKerjaBmo2PamaLayer) {
                        areaKerjaBmo2PamaLayer.setVisible(true);
                        areaKerjaBmo2PamaLayer.setOpacity(1.0);
                        console.log('Showing Area Kerja BMO2 PAMA layer from JS');
                    }
                }
            } else {
                // Switch map layer for other options
                switchMapLayer(layerName);
            }
        }

        console.log('Layer filter initialized with new design');
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLayerFilter);
    } else {
        // DOM already loaded
        setTimeout(initLayerFilter, 100);
    }

    // Create vector layer for SAP (Safety Action Plan) - mengganti hazard
    // Default hidden untuk performa, akan muncul saat toggle diklik
    hazardLayer = new ol.layer.Vector({
        source: new ol.source.Vector(),
        visible: false,  // Default hidden
        style: function(feature) {
            // Check site filter
            if (currentSiteFilter) {
                const sapData = feature.get('data');
                if (sapData) {
                    const sapSite = sapData.site || null;
                    if (sapSite !== currentSiteFilter) {
                        return null; // Hide feature if doesn't match filter
                    }
                } else {
                    return null; // Hide if no data
                }
            }
            
            const jenisLaporan = feature.get('jenis_laporan');
            
            // Warna berdasarkan jenis laporan SAP
            let color = '#3b82f6'; // default blue untuk SAP
            if (jenisLaporan === 'OBSERVASI') color = '#3b82f6';
            else if (jenisLaporan === 'INSPEKSI') color = '#10b981';
            else if (jenisLaporan === 'AUDIT') color = '#f59e0b';
            else color = '#6b7280';
            
            return new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 10,
                    fill: new ol.style.Fill({ color: color }),
                    stroke: new ol.style.Stroke({
                        color: '#ffffff',
                        width: 2
                    })
                })
            });
        },
        zIndex: 1000  // Z-index tinggi agar selalu di atas WMS layer
    });
    map.addLayer(hazardLayer);

    // Create vector layer for insiden with pulsating circle animation
    insidenLayer = new ol.layer.Vector({
        source: new ol.source.Vector(),
        style: function(feature) {
            // Check site filter
            if (currentSiteFilter) {
                const insidenData = feature.get('data');
                if (insidenData) {
                    const insidenSite = insidenData.site || null;
                    if (insidenSite !== currentSiteFilter) {
                        return null; // Hide feature if doesn't match filter
                    }
                } else {
                    return null; // Hide if no data
                }
            }
            
            // Get pulsating animation values
            const animTime = getPulseAnimationTime();
            const pulse = getPulsatingCircleValues(animTime);
            
            // Base radius for the inner dot (15px in CSS, but we'll use 9px to match original)
            const baseRadius = 9;
            
            // Outer pulsating ring (expanding circle)
            const ringRadius = baseRadius * pulse.ringScale;
            const ringStyle = new ol.style.Style({
                image: new ol.style.Circle({
                    radius: ringRadius,
                    fill: null, // No fill, just stroke like CSS
                    stroke: new ol.style.Stroke({
                        color: `rgba(1, 164, 233, ${pulse.ringOpacity})`, // #01a4e9
                        width: 2
                    })
                }),
                zIndex: 0 // Behind the dot
            });
            
            // Inner pulsating dot (main circle)
            const dotRadius = baseRadius * pulse.dotScale;
            const dotStyle = new ol.style.Style({
                image: new ol.style.Circle({
                    radius: dotRadius,
                    fill: new ol.style.Fill({ color: '#ffffff' }), // White center
                    stroke: new ol.style.Stroke({
                        color: '#f97316', // Orange border
                        width: 2
                    })
                }),
                zIndex: 1 // Above the ring
            });
            
            // Return array of styles (ring + dot) for pulsating effect
            return [ringStyle, dotStyle];
        },
        zIndex: 1001
    });
    map.addLayer(insidenLayer);

    // Function to get a point inside polygon (ensures point is within boundary)
    function getPointInPolygon(geometry) {
        if (!geometry || geometry.getType() === 'Point') {
            return geometry ? geometry.getCoordinates() : null;
        }
        
        const extent = geometry.getExtent();
        const center = ol.extent.getCenter(extent);
        
        // Check if center is inside polygon
        if (geometry.intersectsCoordinate(center)) {
            return center;
        }
        
        // If center is not inside, try to find a point inside
        // Use the centroid or try points along the diagonal
        const coords = geometry.getCoordinates();
        if (geometry.getType() === 'Polygon' && coords[0] && coords[0].length > 0) {
            // Try to find a point inside by checking multiple points
            const ring = coords[0];
            let insidePoint = null;
            
            // Try center first
            if (geometry.intersectsCoordinate(center)) {
                return center;
            }
            
            // Try points along the ring (every 10th point)
            for (let i = 0; i < ring.length; i += 10) {
                const testPoint = ring[i];
                if (geometry.intersectsCoordinate(testPoint)) {
                    insidePoint = testPoint;
                    break;
                }
            }
            
            // If still not found, use the first coordinate of the ring
            if (!insidePoint && ring.length > 0) {
                insidePoint = ring[0];
            }
            
            return insidePoint || center;
        }
        
        return center;
    }
    
    // Function to create card icon with arrow pointing down
    function createCardIcon(fotoUrl, pekerjaan, callback) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const cardWidth = 200;
        const cardHeight = 180;
        const arrowHeight = 20;
        const totalHeight = cardHeight + arrowHeight;
        
        canvas.width = cardWidth;
        canvas.height = totalHeight;
        
        // Draw white card background
        ctx.fillStyle = '#ffffff';
        ctx.beginPath();
        ctx.moveTo(10, 0);
        ctx.lineTo(cardWidth - 10, 0);
        ctx.lineTo(cardWidth, 10);
        ctx.lineTo(cardWidth, cardHeight - 10);
        ctx.lineTo(cardWidth - 10, cardHeight);
        ctx.lineTo(10, cardHeight);
        ctx.lineTo(0, cardHeight - 10);
        ctx.lineTo(0, 10);
        ctx.closePath();
        ctx.fill();
        
        // Draw shadow
        ctx.shadowColor = 'rgba(0, 0, 0, 0.2)';
        ctx.shadowBlur = 10;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 2;
        ctx.fill();
        
        // Reset shadow
        ctx.shadowColor = 'transparent';
        ctx.shadowBlur = 0;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 0;
        
        // Draw card border (gray border)
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = 1;
        ctx.stroke();
        
        // Draw red blinking border for attention (like notification pulse)
        ctx.strokeStyle = 'rgba(234, 67, 53, 0.8)'; // Red color like notification
        ctx.lineWidth = 3;
        ctx.stroke();
        
        // Draw "DOP" text at top
        ctx.fillStyle = '#1f2937';
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        ctx.fillText('DOP', cardWidth / 2, 12);
        
        // Draw image placeholder or load actual image
        const imageY = 35;
        const imageHeight = 100;
        const imageWidth = cardWidth - 20;
        const imageX = 10;
        
        if (fotoUrl) {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                // Clear canvas and redraw everything
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Draw white card background
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.moveTo(10, 0);
                ctx.lineTo(cardWidth - 10, 0);
                ctx.lineTo(cardWidth, 10);
                ctx.lineTo(cardWidth, cardHeight - 10);
                ctx.lineTo(cardWidth - 10, cardHeight);
                ctx.lineTo(10, cardHeight);
                ctx.lineTo(0, cardHeight - 10);
                ctx.lineTo(0, 10);
                ctx.closePath();
                ctx.fill();
                
                // Draw shadow
                ctx.shadowColor = 'rgba(0, 0, 0, 0.2)';
                ctx.shadowBlur = 10;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 2;
                ctx.fill();
                
                // Reset shadow
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
                
                // Draw card border (gray border)
                ctx.strokeStyle = '#e5e7eb';
                ctx.lineWidth = 1;
                ctx.stroke();
                
                // Draw red blinking border for attention (like notification pulse)
                ctx.strokeStyle = 'rgba(234, 67, 53, 0.8)'; // Red color like notification
                ctx.lineWidth = 3;
                ctx.stroke();
                
                // Draw "DOP" text at top
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 14px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';
                ctx.fillText('DOP', cardWidth / 2, 12);
                
                // Draw image with rounded corners
                ctx.save();
                ctx.beginPath();
                // Draw rounded rectangle path
                const radius = 4;
                ctx.moveTo(imageX + radius, imageY);
                ctx.lineTo(imageX + imageWidth - radius, imageY);
                ctx.quadraticCurveTo(imageX + imageWidth, imageY, imageX + imageWidth, imageY + radius);
                ctx.lineTo(imageX + imageWidth, imageY + imageHeight - radius);
                ctx.quadraticCurveTo(imageX + imageWidth, imageY + imageHeight, imageX + imageWidth - radius, imageY + imageHeight);
                ctx.lineTo(imageX + radius, imageY + imageHeight);
                ctx.quadraticCurveTo(imageX, imageY + imageHeight, imageX, imageY + imageHeight - radius);
                ctx.lineTo(imageX, imageY + radius);
                ctx.quadraticCurveTo(imageX, imageY, imageX + radius, imageY);
                ctx.closePath();
                ctx.clip();
                ctx.drawImage(img, imageX, imageY, imageWidth, imageHeight);
                ctx.restore();
                
                // Draw pekerjaan name below image
                ctx.fillStyle = '#1f2937';
                ctx.font = '12px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';
                const pekerjaanText = pekerjaan.length > 25 ? pekerjaan.substring(0, 25) + '...' : pekerjaan;
                ctx.fillText(pekerjaanText, cardWidth / 2, imageY + imageHeight + 5);
                
                // Draw arrow pointing down
                const arrowX = cardWidth / 2;
                const arrowY = cardHeight;
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.moveTo(arrowX - 15, arrowY);
                ctx.lineTo(arrowX, arrowY + arrowHeight);
                ctx.lineTo(arrowX + 15, arrowY);
                ctx.closePath();
                ctx.fill();
                
                // Draw arrow border
                ctx.strokeStyle = '#e5e7eb';
                ctx.lineWidth = 1;
                ctx.stroke();
                
                callback(canvas.toDataURL());
            };
            img.onerror = function() {
                // Clear canvas and redraw everything
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Draw white card background
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.moveTo(10, 0);
                ctx.lineTo(cardWidth - 10, 0);
                ctx.lineTo(cardWidth, 10);
                ctx.lineTo(cardWidth, cardHeight - 10);
                ctx.lineTo(cardWidth - 10, cardHeight);
                ctx.lineTo(10, cardHeight);
                ctx.lineTo(0, cardHeight - 10);
                ctx.lineTo(0, 10);
                ctx.closePath();
                ctx.fill();
                
                // Draw shadow
                ctx.shadowColor = 'rgba(0, 0, 0, 0.2)';
                ctx.shadowBlur = 10;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 2;
                ctx.fill();
                
                // Reset shadow
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
                
                // Draw card border (gray border)
                ctx.strokeStyle = '#e5e7eb';
                ctx.lineWidth = 1;
                ctx.stroke();
                
                // Draw red blinking border for attention (like notification pulse)
                ctx.strokeStyle = 'rgba(234, 67, 53, 0.8)'; // Red color like notification
                ctx.lineWidth = 3;
                ctx.stroke();
                
                // Draw "DOP" text at top
                ctx.fillStyle = '#1f2937';
                ctx.font = 'bold 14px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';
                ctx.fillText('DOP', cardWidth / 2, 12);
                
                // If image fails to load, draw placeholder
                ctx.fillStyle = '#f3f4f6';
                ctx.fillRect(imageX, imageY, imageWidth, imageHeight);
                ctx.fillStyle = '#9ca3af';
                ctx.font = '11px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('Gambar tidak tersedia', cardWidth / 2, imageY + imageHeight / 2);
                
                // Draw pekerjaan name
                ctx.fillStyle = '#1f2937';
                ctx.font = '12px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';
                const pekerjaanText = pekerjaan.length > 25 ? pekerjaan.substring(0, 25) + '...' : pekerjaan;
                ctx.fillText(pekerjaanText, cardWidth / 2, imageY + imageHeight + 5);
                
                // Draw arrow
                const arrowX = cardWidth / 2;
                const arrowY = cardHeight;
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.moveTo(arrowX - 15, arrowY);
                ctx.lineTo(arrowX, arrowY + arrowHeight);
                ctx.lineTo(arrowX + 15, arrowY);
                ctx.closePath();
                ctx.fill();
                ctx.strokeStyle = '#e5e7eb';
                ctx.lineWidth = 1;
                ctx.stroke();
                
                callback(canvas.toDataURL());
            };
            img.src = fotoUrl;
        } else {
            // No image, just show pekerjaan name
            ctx.fillStyle = '#f3f4f6';
            ctx.fillRect(imageX, imageY, imageWidth, imageHeight);
            ctx.fillStyle = '#1f2937';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            const pekerjaanText = pekerjaan.length > 30 ? pekerjaan.substring(0, 30) + '...' : pekerjaan;
            ctx.fillText(pekerjaanText, cardWidth / 2, imageY + imageHeight / 2);
            
            // Draw arrow
            const arrowX = cardWidth / 2;
            const arrowY = cardHeight;
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.moveTo(arrowX - 15, arrowY);
            ctx.lineTo(arrowX, arrowY + arrowHeight);
            ctx.lineTo(arrowX + 15, arrowY);
            ctx.closePath();
            ctx.fill();
            ctx.strokeStyle = '#e5e7eb';
            ctx.lineWidth = 1;
            ctx.stroke();
            
            callback(canvas.toDataURL());
        }
    }
    
    // Cache for card icons
    const cardIconCache = {};
    
    // Create vector layer for Daily Operation Plans (Matriks Area Kerja)
    dailyOperationPlansLayer = new ol.layer.Vector({
source: new ol.source.Vector(),
        visible: false,  // Hidden by default, will be shown when traffic layer is toggled
        style: function(feature, resolution) {
            const props = feature.getProperties();
            const geometry = feature.getGeometry();
            
            // Style based on potensi_resiko or use default color
            let fillColor = 'rgba(59, 130, 246, 0.3)'; // Blue default
            let strokeColor = '#3b82f6';
            let strokeWidth = 2;
            
            // Different colors based on potensi_resiko if available
            const potensiResiko = props.potensi_resiko || '';
            if (potensiResiko.toLowerCase().includes('tinggi') || potensiResiko.toLowerCase().includes('high')) {
                fillColor = 'rgba(239, 68, 68, 0.4)'; // Red for high risk
                strokeColor = '#ef4444';
                strokeWidth = 3;
            } else if (potensiResiko.toLowerCase().includes('sedang') || potensiResiko.toLowerCase().includes('medium')) {
                fillColor = 'rgba(245, 158, 11, 0.4)'; // Orange for medium risk
                strokeColor = '#f59e0b';
                strokeWidth = 2.5;
            } else if (potensiResiko.toLowerCase().includes('rendah') || potensiResiko.toLowerCase().includes('low')) {
                fillColor = 'rgba(16, 185, 129, 0.3)'; // Green for low risk
                strokeColor = '#10b981';
            }
            
            const styles = [
                // Polygon fill and stroke
                new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: fillColor
                    }),
                    stroke: new ol.style.Stroke({
                        color: strokeColor,
                        width: strokeWidth
                    })
                })
            ];
            
            // Get point inside polygon for label placement
            if (geometry && geometry.getType() !== 'Point') {
                const pointInPolygon = getPointInPolygon(geometry);
                
                if (pointInPolygon) {
                    // Check if feature has card icon stored
                    const cardIcon = feature.get('cardIcon');
                    
                    if (cardIcon) {
                        // Calculate blink opacity untuk efek ripple yang lebih menarik
                        // Multiple ripple circles dengan delay berbeda untuk efek yang lebih dramatis
                        let blinkTime = 0;
                        if (pulseAnimationStartTime !== null) {
                            blinkTime = getPulseAnimationTime();
                        }
                        
                        // Use the card icon
                        styles.push(new ol.style.Style({
                            image: new ol.style.Icon({
                                src: cardIcon,
                                scale: 0.5, // Scale down for map
                                anchor: [0.5, 1], // Anchor at bottom center (arrow tip)
                                anchorXUnits: 'fraction',
                                anchorYUnits: 'fraction',
                                opacity: 1
                            }),
                            geometry: new ol.geom.Point(pointInPolygon),
                            zIndex: 1000
                        }));
                        
                        // Add multiple red blinking ripple circles for dramatic effect
                        // Ripple 1: Outer circle (largest, expanding)
                        const cycle1 = 2000; // 2 second cycle
                        const progress1 = ((blinkTime % cycle1) / cycle1);
                        const ripple1Radius = 40 + (progress1 * 40); // Expand from 40 to 80
                        const ripple1Opacity = Math.max(0, 0.8 * (1 - progress1)); // Fade out
                        const ripple1Width = 4;
                        
                        styles.push(new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: ripple1Radius,
                                fill: new ol.style.Fill({
                                    color: 'rgba(234, 67, 53, 0)'
                                }),
                                stroke: new ol.style.Stroke({
                                    color: `rgba(234, 67, 53, ${ripple1Opacity})`,
                                    width: ripple1Width
                                })
                            }),
                            geometry: new ol.geom.Point(pointInPolygon),
                            zIndex: 998
                        }));
                        
                        // Ripple 2: Middle circle (delayed by 0.4s)
                        const progress2 = (((blinkTime + 800) % cycle1) / cycle1);
                        const ripple2Radius = 40 + (progress2 * 40);
                        const ripple2Opacity = Math.max(0, 0.6 * (1 - progress2));
                        const ripple2Width = 3;
                        
                        styles.push(new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: ripple2Radius,
                                fill: new ol.style.Fill({
                                    color: 'rgba(234, 67, 53, 0)'
                                }),
                                stroke: new ol.style.Stroke({
                                    color: `rgba(234, 67, 53, ${ripple2Opacity})`,
                                    width: ripple2Width
                                })
                            }),
                            geometry: new ol.geom.Point(pointInPolygon),
                            zIndex: 997
                        }));
                        
                        // Ripple 3: Inner circle (delayed by 0.8s)
                        const progress3 = (((blinkTime + 1600) % cycle1) / cycle1);
                        const ripple3Radius = 40 + (progress3 * 40);
                        const ripple3Opacity = Math.max(0, 0.5 * (1 - progress3));
                        const ripple3Width = 2;
                        
                        styles.push(new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: ripple3Radius,
                                fill: new ol.style.Fill({
                                    color: 'rgba(234, 67, 53, 0)'
                                }),
                                stroke: new ol.style.Stroke({
                                    color: `rgba(234, 67, 53, ${ripple3Opacity})`,
                                    width: ripple3Width
                                })
                            }),
                            geometry: new ol.geom.Point(pointInPolygon),
                            zIndex: 996
                        }));
                        
                        // Pulsing dot at center (always visible, pulsing)
                        const dotCycle = 1000; // 1 second for faster pulse
                        const dotProgress = ((blinkTime % dotCycle) / dotCycle);
                        const dotRadius = 8 + (Math.sin(dotProgress * Math.PI * 2) * 3); // Pulse from 5 to 11
                        const dotOpacity = 0.7 + (Math.sin(dotProgress * Math.PI * 2) * 0.3); // Pulse opacity
                        
                        styles.push(new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: dotRadius,
                                fill: new ol.style.Fill({
                                    color: `rgba(234, 67, 53, ${dotOpacity})`
                                }),
                                stroke: new ol.style.Stroke({
                                    color: `rgba(234, 67, 53, ${Math.min(1, dotOpacity + 0.2)})`,
                                    width: 2
                                })
                            }),
                            geometry: new ol.geom.Point(pointInPolygon),
                            zIndex: 999
                        }));
                        
                        // Glow effect: Large soft circle for dramatic glow
                        const glowCycle = 1500; // 1.5 second cycle
                        const glowProgress = ((blinkTime % glowCycle) / glowCycle);
                        const glowRadius = 60 + (Math.sin(glowProgress * Math.PI * 2) * 20); // Pulse from 40 to 80
                        const glowOpacity = 0.3 + (Math.sin(glowProgress * Math.PI * 2) * 0.2); // Pulse opacity 0.1 to 0.5
                        
                        styles.push(new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: glowRadius,
                                fill: new ol.style.Fill({
                                    color: `rgba(234, 67, 53, ${glowOpacity})`
                                })
                            }),
                            geometry: new ol.geom.Point(pointInPolygon),
                            zIndex: 995
                        }));
                        
                        // Additional outer glow ring (larger, more transparent)
                        const outerGlowProgress = ((blinkTime % glowCycle) / glowCycle);
                        const outerGlowRadius = 80 + (Math.sin(outerGlowProgress * Math.PI * 2) * 30); // Pulse from 50 to 110
                        const outerGlowOpacity = 0.15 + (Math.sin(outerGlowProgress * Math.PI * 2) * 0.1); // Pulse opacity 0.05 to 0.25
                        
                        styles.push(new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: outerGlowRadius,
                                fill: new ol.style.Fill({
                                    color: `rgba(234, 67, 53, ${outerGlowOpacity})`
                                })
                            }),
                            geometry: new ol.geom.Point(pointInPolygon),
                            zIndex: 994
                        }));
                    } else {
                        // Use placeholder while icon is being created
                        styles.push(new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: 8,
                                fill: new ol.style.Fill({ color: '#ffffff' }),
                                stroke: new ol.style.Stroke({ color: strokeColor, width: 2 })
                            }),
                            geometry: new ol.geom.Point(pointInPolygon),
                            zIndex: 1000
                        }));
                    }
                }
            }
            
            return styles;
        },
        name: 'Daily Operation Plans',
        zIndex: 450  // Above area kerja layers but below markers
    });
    map.addLayer(dailyOperationPlansLayer);

    // Function to load daily operation plans from API
    function loadDailyOperationPlans() {
        console.log('Loading daily operation plans...');
        const source = dailyOperationPlansLayer.getSource();
        
        // Clear existing features first to reload
        source.clear();
        console.log('Cleared existing features from layer');
        
        fetch('{{ url("full-maps/api/daily-operation-plans") }}')
            .then(response => response.json())
                .then(data => {
                    console.log('Daily operation plans API response:', data);
                    
                    // Log summary if available
                    if (data.summary) {
                        console.log('API Summary:', {
                            total_plans: data.summary.total_plans,
                            processed: data.summary.processed,
                            found_in_clickhouse: data.summary.found_in_clickhouse,
                            with_geometry: data.summary.with_geometry,
                            features_returned: data.summary.features_returned,
                            plans_not_found: data.summary.plans_not_found
                        });
                    }
                    
                    if (data.success && data.data && data.data.features) {
                    const geoJsonData = data.data;
                    console.log(`Received ${geoJsonData.features.length} features from API`);
                    
                    // Helper function to get coordinates structure info
                    function getCoordinatesStructure(coords, type) {
                        if (!Array.isArray(coords)) return 'not_array';
                        if (coords.length === 0) return 'empty';
                        
                        if (type === 'Polygon') {
                            // Polygon harus: [[[lon, lat], [lon, lat], ...], ...]
                            if (coords.length > 0 && Array.isArray(coords[0])) {
                                if (coords[0].length > 0 && Array.isArray(coords[0][0])) {
                                    if (coords[0][0].length >= 2 && typeof coords[0][0][0] === 'number') {
                                        return `Polygon: ${coords.length} rings, first ring has ${coords[0].length} points`;
                                    } else {
                                        return `Polygon: invalid nested structure - ring[0] is not [lon, lat]`;
                                    }
                                } else {
                                    return `Polygon: ring[0] is not an array`;
                                }
                            } else {
                                return `Polygon: coords[0] is not an array`;
                            }
                        } else if (type === 'MultiPolygon') {
                            // MultiPolygon harus: [[[[lon, lat], ...], ...], ...]
                            if (coords.length > 0 && Array.isArray(coords[0])) {
                                if (coords[0].length > 0 && Array.isArray(coords[0][0])) {
                                    if (coords[0][0].length > 0 && Array.isArray(coords[0][0][0])) {
                                        if (coords[0][0][0].length >= 2 && typeof coords[0][0][0][0] === 'number') {
                                            return `MultiPolygon: ${coords.length} polygons`;
                                        } else {
                                            return `MultiPolygon: invalid nested structure`;
                                        }
                                    } else {
                                        return `MultiPolygon: polygon[0][0] is not an array`;
                                    }
                                } else {
                                    return `MultiPolygon: polygon[0] is not an array`;
                                }
                            } else {
                                return `MultiPolygon: coords[0] is not an array`;
                            }
                        }
                        return `unknown_structure: type=${type}, coords.length=${coords.length}`;
                    }
                    
                    // Parse GeoJSON features dengan error handling
                    let features = [];
                    try {
                        // Validasi setiap feature sebelum parsing
                        if (geoJsonData.features && Array.isArray(geoJsonData.features)) {
                            geoJsonData.features.forEach((feature, index) => {
                                try {
                                    // Validasi feature structure
                                    if (!feature.geometry) {
                                        console.warn(`Feature ${index} has no geometry, skipping`);
                                        return;
                                    }
                                    
                                    if (!feature.geometry.type) {
                                        console.warn(`Feature ${index} geometry has no type, skipping`);
                                        return;
                                    }
                                    
                                    if (!feature.geometry.coordinates) {
                                        console.warn(`Feature ${index} geometry has no coordinates, skipping`);
                                        return;
                                    }
                                    
                                    // Validasi coordinates structure
                                    const coords = feature.geometry.coordinates;
                                    if (!Array.isArray(coords)) {
                                        console.warn(`Feature ${index} coordinates is not an array:`, typeof coords);
                                        return;
                                    }
                                    
                                    // Log geometry structure untuk debugging
                                    const structureInfo = getCoordinatesStructure(coords, feature.geometry.type);
                                    console.log(`Feature ${index} geometry:`, {
                                        type: feature.geometry.type,
                                        coordinates_length: coords.length,
                                        coordinates_structure: structureInfo,
                                        coordinates_preview: JSON.stringify(coords).substring(0, 200)
                                    });
                                    
                                    // Jika struktur tidak valid, coba perbaiki
                                    if (structureInfo.includes('unknown') || structureInfo.includes('invalid')) {
                                        console.warn(`Feature ${index} has invalid coordinates structure, attempting to fix...`);
                                        
                                        // Coba perbaiki struktur untuk Polygon
                                        if (feature.geometry.type === 'Polygon' && coords.length === 1 && Array.isArray(coords[0])) {
                                            // Mungkin coordinates adalah [[[lon, lat], ...]] tapi perlu di-wrap lagi
                                            const firstRing = coords[0];
                                            if (Array.isArray(firstRing) && firstRing.length > 0) {
                                                if (Array.isArray(firstRing[0]) && firstRing[0].length >= 2) {
                                                    // Ini sudah benar, tapi mungkin perlu di-wrap
                                                    feature.geometry.coordinates = [firstRing];
                                                    console.log(`Fixed Polygon coordinates structure for feature ${index}`);
                                                } else if (typeof firstRing[0] === 'number' && firstRing.length >= 2) {
                                                    // Ini adalah ring yang belum di-wrap: [[lon, lat], ...]
                                                    feature.geometry.coordinates = [firstRing];
                                                    console.log(`Fixed Polygon coordinates: wrapped single ring for feature ${index}`);
                                                }
                                            }
                                        }
                                    }
                                    
                                    // Parse single feature
                                    const parsedFeature = new ol.format.GeoJSON().readFeatures({
                                        type: 'FeatureCollection',
                                        features: [feature]
                                    }, {
                                        dataProjection: 'EPSG:4326',
                                        featureProjection: 'EPSG:3857'
                                    });
                                    
                                    if (parsedFeature && parsedFeature.length > 0) {
                                        features.push(parsedFeature[0]);
                                    }
                                } catch (featureError) {
                                    console.error(`Error parsing feature ${index}:`, featureError);
                                    console.error('Feature data:', feature);
                                }
                            });
                        }
                    } catch (parseError) {
                        console.error('Error parsing GeoJSON:', parseError);
                        console.error('GeoJSON data:', geoJsonData);
                    }
                    
                    console.log(`Parsed ${features.length} features from GeoJSON`);
                    
                    // Debug: log setiap feature
                    features.forEach((feature, index) => {
                        const geometry = feature.getGeometry();
                        const props = feature.getProperties();
                        console.log(`Feature ${index}:`, {
                            hasGeometry: !!geometry,
                            geometryType: geometry ? geometry.getType() : 'none',
                            properties: props
                        });
                        
                        if (geometry) {
                            const extent = geometry.getExtent();
                            console.log(`Feature ${index} extent:`, extent);
                        }
                    });
                    
                    // Tidak ada validasi ketat - langsung tampilkan semua features yang punya geometry
                    const validFeatures = features.filter(feature => {
                        const geometry = feature.getGeometry();
                        if (!geometry) {
                            console.warn('Feature has no geometry, skipping:', feature.getProperties());
                            return false;
                        }
                        // Hanya cek apakah ada geometry, tidak ada validasi lainnya
                        return true;
                    });
                    
                    console.log(`Valid features: ${validFeatures.length} out of ${features.length}`);
                    
                    // Add valid features to layer
                    if (validFeatures.length > 0) {
                        try {
                            source.addFeatures(validFeatures);
                            console.log(`Successfully added ${validFeatures.length} features to layer`);
                            
                            // Create card icons for all features
                            let iconsToCreate = 0;
                            let iconsCreated = 0;
                            
                            validFeatures.forEach((feature) => {
                                const props = feature.getProperties();
                                const pekerjaan = props.pekerjaan || 'N/A';
                                const fotoPekerjaan = props.foto_pekerjaan || null;
                                const fotoUrl = fotoPekerjaan ? `{{ asset('storage/') }}/${fotoPekerjaan}` : null;
                                
                                // Create cache key
                                const cacheKey = `${fotoUrl || 'no-image'}_${pekerjaan}`;
                                
                                // Create icon if not already cached
                                if (!cardIconCache[cacheKey]) {
                                    iconsToCreate++;
                                    createCardIcon(fotoUrl, pekerjaan, function(dataUrl) {
                                        cardIconCache[cacheKey] = dataUrl;
                                        feature.set('cardIcon', dataUrl);
                                        iconsCreated++;
                                        
                                        // Trigger layer update when all icons are created
                                        if (iconsCreated === iconsToCreate) {
                                            dailyOperationPlansLayer.changed();
                                        }
                                    });
                                } else {
                                    feature.set('cardIcon', cardIconCache[cacheKey]);
                                }
                            });
                            
                            // If all icons were already cached, trigger update immediately
                            if (iconsToCreate === 0) {
                                dailyOperationPlansLayer.changed();
                            }
                            
                            // Update notification panel if it's open
                            setTimeout(() => {
                                const notificationPanel = document.getElementById('gmNotificationPanel');
                                if (notificationPanel && notificationPanel.classList.contains('active')) {
                                    renderNotificationPanel();
                                }
                            }, 500);
                            
                            // Check if features are actually in the source
                            const featuresInSource = source.getFeatures();
                            console.log(`Features in source after add: ${featuresInSource.length}`);
                            
                            // Check layer visibility
                            console.log('Layer visible:', dailyOperationPlansLayer.getVisible());
                            console.log('Layer source features:', dailyOperationPlansLayer.getSource().getFeatures().length);
                            
                            // Fit map to show all plans if there are any
                            const extent = source.getExtent();
                            console.log('Source extent:', extent);
                            if (extent && extent[0] !== Infinity && extent[1] !== Infinity) {
                                map.getView().fit(extent, {
                                    padding: [50, 50, 50, 50],
                                    maxZoom: 18
                                });
                                console.log('Map fitted to extent');
                            } else {
                                console.warn('Invalid extent, cannot fit map');
                            }
                        } catch (error) {
                            console.error('Error adding features to layer:', error);
                        }
                    } else {
                        console.warn('No valid features to add');
                    }
                } else {
                    console.warn('No daily operation plans data received');
                }
            })
            .catch(error => {
                console.error('Error loading daily operation plans:', error);
            });
    }

    // Add SAP markers (mengganti hazard markers) - OPTIMIZED dengan batch rendering
    // Limit jumlah marker untuk performa (maksimal 1000 marker)
    const MAX_SAP_MARKERS = 1000;
    const BATCH_SIZE = 100; // Render dalam batch untuk performa
    
    function addSapMarkersBatch(sapDataArray) {
        if (!sapDataArray || sapDataArray.length === 0) return;
        
        let markerCount = 0;
        const source = hazardLayer.getSource();
        const features = [];
        
        // Filter dan prepare features
        sapDataArray.forEach(function(sap) {
            if (markerCount >= MAX_SAP_MARKERS) return;
            if (!sap.location || !sap.location.lat || !sap.location.lng) return;
            
            const feature = new ol.Feature({
                geometry: new ol.geom.Point(
                    ol.proj.fromLonLat([sap.location.lng, sap.location.lat])
                ),
                id: sap.id,
                task_number: sap.task_number,
                jenis_laporan: sap.jenis_laporan,
                aktivitas_pekerjaan: sap.aktivitas_pekerjaan,
                lokasi: sap.lokasi,
                description: sap.description,
                data: sap
            });
            features.push(feature);
            markerCount++;
        });
        
        // Batch add features untuk performa
        if (features.length > 0) {
            // Add dalam batch menggunakan requestAnimationFrame
            let index = 0;
            function addBatch() {
                const batch = features.slice(index, index + BATCH_SIZE);
                if (batch.length > 0) {
                    source.addFeatures(batch);
                    index += BATCH_SIZE;
                    if (index < features.length) {
                        requestAnimationFrame(addBatch);
                    } else {
                        console.log(`Added ${features.length} SAP markers to map (filtered for today) in batches`);
                    }
                }
            }
            requestAnimationFrame(addBatch);
        }
    }
    
    // Defer marker rendering sampai map ready
    setTimeout(() => {
        addSapMarkersBatch(sapData);
    }, 500);

    // function createCCTVIcon({ fill = "#a142f4", live = false } = {}) {
    //         const W = 56, H = 56;
    //         const dpr = Math.max(1, Math.min(3, window.devicePixelRatio || 1));

    //         const c = document.createElement("canvas");
    //         c.width = Math.round(W * dpr);
    //         c.height = Math.round(H * dpr);
    //         const ctx = c.getContext("2d");
    //         ctx.setTransform(dpr,0,0,dpr,0,0);
    //         ctx.clearRect(0,0,W,H);

    //         const cx = 28, cy = 22;
    //         const Rw = 16.5;     // radius warna
    //         const Rb = 20.5;     // radius border putih
    //         const tailH = 12;    // tinggi tail
    //         const tailW = 16;    // lebar tail

    //         function bubblePath(R){
    //             const baseY = cy + R * 0.78;
    //             const dy = baseY - cy;
    //             const dx = Math.sqrt(Math.max(0, R*R - dy*dy));
    //             const xL = cx - dx, xR = cx + dx;
    //             const aL = Math.atan2(dy, -dx);
    //             const aR = Math.atan2(dy,  dx);

    //             ctx.beginPath();
    //             ctx.moveTo(xL, baseY);
    //             ctx.arc(cx, cy, R, aL, aR, true);

    //             const half = tailW / 2;
    //             const tipX = cx;
    //             const tipY = baseY + tailH;

    //             ctx.quadraticCurveTo(cx + half, baseY + 2, cx + half*0.7, baseY + tailH*0.55);
    //             ctx.quadraticCurveTo(cx + half*0.25, baseY + tailH*0.9, tipX, tipY);
    //             ctx.quadraticCurveTo(cx - half*0.25, baseY + tailH*0.9, cx - half*0.7, baseY + tailH*0.55);
    //             ctx.quadraticCurveTo(cx - half, baseY + 2, xL, baseY);

    //             ctx.closePath();
    //         }

    //     // shadow
    //     ctx.save();
    //     ctx.shadowColor = "rgba(0,0,0,.28)";
    //     ctx.shadowBlur = 7;
    //     ctx.shadowOffsetY = 3;
    //     bubblePath(Rb);
    //     ctx.fillStyle = "#fff";
    //     ctx.fill();
    //     ctx.restore();

    //     // white border shape
    //     ctx.save();
    //     bubblePath(Rb);
    //     ctx.fillStyle = "#fff";
    //     ctx.fill();
    //     ctx.restore();

    //     // inner fill circle
    //     ctx.save();
    //     ctx.beginPath();
    //     ctx.arc(cx, cy, Rw, 0, Math.PI*2);
    //     ctx.fillStyle = fill;
    //     ctx.fill();
    //     ctx.restore();

    //     // camera glyph
    //     ctx.save();
    //     ctx.translate(cx, cy);
    //     ctx.fillStyle = "#fff";

    //     // body
    //     roundRect(ctx, -10, -6, 20, 12, 3);
    //     ctx.fill();

    //     // lens hole (purple dot)
    //     ctx.beginPath();
    //     ctx.arc(0, 0.5, 4.6, 0, Math.PI*2);
    //     ctx.fillStyle = fill;
    //     ctx.fill();

    //     // lens ring
    //     ctx.beginPath();
    //     ctx.arc(0, 0.5, 4.6, 0, Math.PI*2);
    //     ctx.lineWidth = 1.2;
    //     ctx.strokeStyle = "#fff";
    //     ctx.stroke();

    //     // viewfinder
    //     ctx.fillStyle = "#fff";
    //     roundRect(ctx, -4, -10, 8, 3.5, 1.4);
    //     ctx.fill();

    //     // plus sign top-left
    //     ctx.fillRect(-14, -12, 7, 2.2);
    //     ctx.fillRect(-11.4, -14.6, 2.2, 7);

    //     ctx.restore();

    //     // live dot
    //     if (live) {
    //         ctx.save();
    //         ctx.beginPath();
    //         ctx.arc(cx + 11.5, cy - 11.5, 4, 0, Math.PI*2);
    //         ctx.fillStyle = "#10b981";
    //         ctx.fill();
    //         ctx.lineWidth = 1.8;
    //         ctx.strokeStyle = "#fff";
    //         ctx.stroke();
    //         ctx.restore();
    //     }

    //     return c.toDataURL("image/png");

    //     function roundRect(ctx, x,y,w,h,r){
    //         r = Math.min(r, w/2, h/2);
    //         ctx.beginPath();
    //         ctx.moveTo(x+r, y);
    //         ctx.arcTo(x+w, y, x+w, y+h, r);
    //         ctx.arcTo(x+w, y+h, x, y+h, r);
    //         ctx.arcTo(x, y+h, x, y, r);
    //         ctx.arcTo(x, y, x+w, y, r);
    //         ctx.closePath();
    //     }
    // }

    function createCCTVIcon({
  fill = "#a142f4",  // Default purple - kompatibel dengan pemanggilan yang menggunakan 'fill'
  live = false,
  size = 56,          // 48–64 enak
  glyph = "camera",   // siap kalau mau tambah nanti
} = {}) {
  const W = size, H = size;
  const dpr = Math.max(1, Math.min(3, window.devicePixelRatio || 1));

  const c = document.createElement("canvas");
  c.width = Math.round(W * dpr);
  c.height = Math.round(H * dpr);
  const ctx = c.getContext("2d");
  ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  ctx.clearRect(0, 0, W, H);

  // Geometry (proporsional ke size)
  const cx = W * 0.5;
  const cy = H * 0.40;
  const R_outer = W * 0.37;  // outer white ring shape radius
  const R_inner = W * 0.30;  // inner colored circle radius
  const tailH  = H * 0.22;
  const tailW  = W * 0.30;

  // Alias untuk kompatibilitas (menggunakan 'fill' sebagai parameter utama)
  const color = fill;

  // ---------- helpers ----------
  function bubblePath(R) {
    const baseY = cy + R * 0.78;
    const dy = baseY - cy;
    const dx = Math.sqrt(Math.max(0, R * R - dy * dy));
    const xL = cx - dx, xR = cx + dx;
    const aL = Math.atan2(dy, -dx);
    const aR = Math.atan2(dy,  dx);

    ctx.beginPath();
    ctx.moveTo(xL, baseY);
    ctx.arc(cx, cy, R, aL, aR, true);

    const half = tailW / 2;
    const tipX = cx;
    const tipY = baseY + tailH;

    ctx.quadraticCurveTo(cx + half, baseY + 2, cx + half * 0.72, baseY + tailH * 0.55);
    ctx.quadraticCurveTo(cx + half * 0.26, baseY + tailH * 0.92, tipX, tipY);
    ctx.quadraticCurveTo(cx - half * 0.26, baseY + tailH * 0.92, cx - half * 0.72, baseY + tailH * 0.55);
    ctx.quadraticCurveTo(cx - half, baseY + 2, xL, baseY);

    ctx.closePath();
  }

  function roundRect(x, y, w, h, r) {
    r = Math.min(r, w / 2, h / 2);
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r);
    ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r);
    ctx.arcTo(x, y, x + w, y, r);
    ctx.closePath();
  }

  function shade(hex, amt) {
    // amt: -1..1 (gelap..terang)
    const c = hex.replace("#", "");
    const n = parseInt(c.length === 3 ? c.split("").map(x => x + x).join("") : c, 16);
    let r = (n >> 16) & 255, g = (n >> 8) & 255, b = n & 255;
    r = Math.max(0, Math.min(255, Math.round(r + 255 * amt)));
    g = Math.max(0, Math.min(255, Math.round(g + 255 * amt)));
    b = Math.max(0, Math.min(255, Math.round(b + 255 * amt)));
    return `rgb(${r},${g},${b})`;
  }

  // ---------- draw shadow ----------
  ctx.save();
  ctx.shadowColor = "rgba(0,0,0,.30)";
  ctx.shadowBlur = Math.max(6, W * 0.14);
  ctx.shadowOffsetY = Math.max(2, H * 0.06);
  bubblePath(R_outer);
  ctx.fillStyle = "#fff";
  ctx.fill();
  ctx.restore();

  // ---------- outer white ring ----------
  ctx.save();
  bubblePath(R_outer);
  ctx.fillStyle = "#fff";
  ctx.fill();
  ctx.restore();

  // subtle border stroke (biar crisp)
  ctx.save();
  bubblePath(R_outer);
  ctx.lineWidth = Math.max(1, W * 0.02);
  ctx.strokeStyle = "rgba(0,0,0,.06)";
  ctx.stroke();
  ctx.restore();

  // ---------- inner colored circle (gradient + gloss) ----------
  ctx.save();
  // Base radial gradient (Google-ish depth)
  const g = ctx.createRadialGradient(cx - R_inner * 0.35, cy - R_inner * 0.35, R_inner * 0.15, cx, cy, R_inner);
  g.addColorStop(0.00, shade(color, +0.22));
  g.addColorStop(0.55, color);
  g.addColorStop(1.00, shade(color, -0.18));

  ctx.beginPath();
  ctx.arc(cx, cy, R_inner, 0, Math.PI * 2);
  ctx.fillStyle = g;
  ctx.fill();

  // inner ring highlight
  ctx.lineWidth = Math.max(1.2, W * 0.025);
  ctx.strokeStyle = "rgba(255,255,255,.35)";
  ctx.stroke();

  // gloss highlight (setengah atas)
  ctx.globalCompositeOperation = "screen";
  ctx.beginPath();
  ctx.ellipse(cx - R_inner * 0.18, cy - R_inner * 0.22, R_inner * 0.72, R_inner * 0.58, -0.2, 0, Math.PI * 2);
  const gloss = ctx.createRadialGradient(cx - R_inner * 0.25, cy - R_inner * 0.35, 0, cx, cy, R_inner);
  gloss.addColorStop(0, "rgba(255,255,255,.40)");
  gloss.addColorStop(1, "rgba(255,255,255,0)");
  ctx.fillStyle = gloss;
  ctx.fill();
  ctx.globalCompositeOperation = "source-over";

  ctx.restore();

  // ---------- glyph (kamera, putih) ----------
  ctx.save();
  ctx.translate(cx, cy);
  ctx.fillStyle = "#fff";

  // body
  const bw = W * 0.36, bh = H * 0.20;
  roundRect(-bw / 2, -bh / 2 + H * 0.02, bw, bh, W * 0.045);
  ctx.fill();

  // lens (hole)
  ctx.beginPath();
  ctx.arc(0, H * 0.02, W * 0.085, 0, Math.PI * 2);
  ctx.fillStyle = shade(color, -0.02);
  ctx.fill();

  // lens ring
  ctx.beginPath();
  ctx.arc(0, H * 0.02, W * 0.085, 0, Math.PI * 2);
  ctx.lineWidth = Math.max(1.1, W * 0.02);
  ctx.strokeStyle = "rgba(255,255,255,.95)";
  ctx.stroke();

  // viewfinder top
  ctx.fillStyle = "#fff";
  const vw = W * 0.14, vh = H * 0.05;
  roundRect(-vw / 2, -bh / 2 - vh * 0.9, vw, vh, W * 0.02);
  ctx.fill();

  // small plus (optional accent ala contoh kamu)
  ctx.fillRect(-W * 0.26, -H * 0.23, W * 0.12, H * 0.035);
  ctx.fillRect(-W * 0.215, -H * 0.265, W * 0.035, H * 0.12);

  ctx.restore();

  // ---------- live dot ----------
  if (live) {
    ctx.save();
    const lx = cx + R_inner * 0.72;
    const ly = cy - R_inner * 0.72;
    const rr = Math.max(3.6, W * 0.075);

    ctx.beginPath();
    ctx.arc(lx, ly, rr, 0, Math.PI * 2);
    ctx.fillStyle = "#10b981";
    ctx.fill();

    ctx.lineWidth = Math.max(1.6, W * 0.03);
    ctx.strokeStyle = "#fff";
    ctx.stroke();

    // tiny glow
    ctx.globalAlpha = 0.35;
    ctx.beginPath();
    ctx.arc(lx, ly, rr * 1.9, 0, Math.PI * 2);
    ctx.fillStyle = "#10b981";
    ctx.fill();
    ctx.restore();
  }

  return c.toDataURL("image/png");
}
   

    // Function to create CCTV icon - Google Maps style pin drop shape
//    function createCCTVIcon(cctv) {
//   const W = 48, H = 64;

//   // HiDPI (biar tajam di layar retina)
//   const dpr = Math.max(1, Math.min(3, window.devicePixelRatio || 1));
//   const canvas = document.createElement("canvas");
//   canvas.width = Math.round(W * dpr);
//   canvas.height = Math.round(H * dpr);
//   canvas.style.width = W + "px";
//   canvas.style.height = H + "px";

//   const ctx = canvas.getContext("2d");
//   ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
//   ctx.clearRect(0, 0, W, H);

//   // Helper roundRect (fallback untuk browser lama)
//   function rrect(x, y, w, h, r) {
//     const rr = Math.min(r, w / 2, h / 2);
//     ctx.beginPath();
//     ctx.moveTo(x + rr, y);
//     ctx.arcTo(x + w, y, x + w, y + h, rr);
//     ctx.arcTo(x + w, y + h, x, y + h, rr);
//     ctx.arcTo(x, y + h, x, y, rr);
//     ctx.arcTo(x, y, x + w, y, rr);
//     ctx.closePath();
//   }

//   // === Shadow (Google Maps-style) ===
//   ctx.save();
//   ctx.fillStyle = "rgba(0,0,0,0.22)";
//   ctx.filter = "blur(0.6px)";
//   ctx.beginPath();
//   ctx.ellipse(24, 59.5, 9.5, 3.2, 0, 0, Math.PI * 2);
//   ctx.fill();
//   ctx.restore();

//   // === Pin body (teardrop) ===
//   const cx = 24;
//   const cy = 18;         // pusat “kepala” pin
//   const r  = 14;         // radius kepala pin
//   const tipY = 50;       // ujung pin

//   ctx.save();

//   // Outer drop shadow halus
//   ctx.shadowColor = "rgba(0,0,0,0.25)";
//   ctx.shadowBlur = 6;
//   ctx.shadowOffsetX = 0;
//   ctx.shadowOffsetY = 3;

//   // Pin gradient (lebih mirip default gmaps: top lebih terang)
//   const pinGrad = ctx.createLinearGradient(0, cy - r, 0, tipY);
//   pinGrad.addColorStop(0.0, "#b26cff");
//   pinGrad.addColorStop(0.55, "#9333ea");
//   pinGrad.addColorStop(1.0, "#6d1fbf");

//   ctx.fillStyle = pinGrad;

//   // Path teardrop (bezier)
//   ctx.beginPath();
//   // Mulai dari puncak kepala
//   ctx.moveTo(cx, cy - r);
//   // sisi kiri kepala
//   ctx.bezierCurveTo(cx - r, cy - r, cx - r - 2, cy + r - 2, cx - 2, cy + r);
//   // turun ke tip (ujung)
//   ctx.bezierCurveTo(cx - 6, cy + r + 10, cx - 4, tipY - 2, cx, tipY);
//   // naik sisi kanan
//   ctx.bezierCurveTo(cx + 4, tipY - 2, cx + 6, cy + r + 10, cx + 2, cy + r);
//   // sisi kanan kepala
//   ctx.bezierCurveTo(cx + r + 2, cy + r - 2, cx + r, cy - r, cx, cy - r);
//   ctx.closePath();
//   ctx.fill();

//   // White border tipis (gmaps feel)
//   ctx.shadowColor = "transparent";
//   ctx.lineWidth = 2.2;
//   ctx.strokeStyle = "#ffffff";
//   ctx.stroke();

//   // Highlight glossy di kiri atas (subtle)
//   ctx.globalAlpha = 0.18;
//   const hiGrad = ctx.createRadialGradient(cx - 6, cy - 8, 2, cx - 6, cy - 8, 18);
//   hiGrad.addColorStop(0, "rgba(255,255,255,0.9)");
//   hiGrad.addColorStop(1, "rgba(255,255,255,0)");
//   ctx.fillStyle = hiGrad;
//   ctx.beginPath();
//   ctx.arc(cx - 4, cy - 6, 12, 0, Math.PI * 2);
//   ctx.fill();
//   ctx.globalAlpha = 1;

//   ctx.restore();

//   // === Inner white circle (gmaps signature) ===
//   ctx.save();
//   ctx.fillStyle = "#ffffff";
//   ctx.beginPath();
//   ctx.arc(cx, cy, 10.6, 0, Math.PI * 2);
//   ctx.fill();

//   // sedikit outline abu (depth)
//   ctx.strokeStyle = "rgba(0,0,0,0.08)";
//   ctx.lineWidth = 1;
//   ctx.stroke();
//   ctx.restore();

//   // === Camera glyph (warna mengikuti pin) ===
//   ctx.save();
//   ctx.translate(cx, cy);

//   const glyph = "#7e22ce"; // senada pin (lebih “gmaps-like” daripada putih)
//   ctx.fillStyle = glyph;

//   // Body kamera
//   rrect(-7.2, -5.4, 14.4, 10.8, 2.2);
//   ctx.fill();

//   // “Cut out” lens pakai compositing supaya terlihat seperti glyph gmaps
//   ctx.globalCompositeOperation = "destination-out";
//   ctx.beginPath();
//   ctx.arc(0, 0.2, 3.4, 0, Math.PI * 2);
//   ctx.fill();

//   // balik normal
//   ctx.globalCompositeOperation = "source-over";

//   // Ring lens tipis (kecil) biar tetap kebaca
//   ctx.strokeStyle = glyph;
//   ctx.lineWidth = 1.6;
//   ctx.beginPath();
//   ctx.arc(0, 0.2, 3.8, 0, Math.PI * 2);
//   ctx.stroke();

//   // Viewfinder atas
//   ctx.fillStyle = glyph;
//   rrect(-3.2, -8.3, 6.4, 2.4, 1.1);
//   ctx.fill();

//   // Plus kecil (lebih rapi & proporsional)
//   ctx.fillStyle = glyph;
//   ctx.fillRect(-9.2, -8.6, 5.2, 1.7);
//   ctx.fillRect(-7.6, -10.1, 1.7, 5.2);

//   ctx.restore();

//   // === Live indicator (tetap) ===
//   if (cctv.status === "Live View" || cctv.status === "live") {
//     ctx.save();
//     // posisi “gmaps-like” di kanan-atas kepala
//     const lx = cx + 10.5;
//     const ly = cy - 10.5;

//     // ring
//     ctx.globalAlpha = 0.28;
//     ctx.strokeStyle = "#10b981";
//     ctx.lineWidth = 2;
//     ctx.beginPath();
//     ctx.arc(lx, ly, 6.2, 0, Math.PI * 2);
//     ctx.stroke();

//     // dot
//     ctx.globalAlpha = 1;
//     const liveGrad = ctx.createRadialGradient(lx, ly, 0, lx, ly, 4);
//     liveGrad.addColorStop(0, "#34d399");
//     liveGrad.addColorStop(0.7, "#10b981");
//     liveGrad.addColorStop(1, "#059669");
//     ctx.fillStyle = liveGrad;
//     ctx.beginPath();
//     ctx.arc(lx, ly, 4, 0, Math.PI * 2);
//     ctx.fill();

//     // border putih
//     ctx.strokeStyle = "#ffffff";
//     ctx.lineWidth = 1.5;
//     ctx.beginPath();
//     ctx.arc(lx, ly, 4, 0, Math.PI * 2);
//     ctx.stroke();

//     ctx.restore();
//   }

//   return canvas.toDataURL("image/png");
// }

    
    // Helper function to draw rounded rectangle
    if (!CanvasRenderingContext2D.prototype.roundRect) {
        CanvasRenderingContext2D.prototype.roundRect = function(x, y, width, height, radius) {
            this.beginPath();
            this.moveTo(x + radius, y);
            this.lineTo(x + width - radius, y);
            this.quadraticCurveTo(x + width, y, x + width, y + radius);
            this.lineTo(x + width, y + height - radius);
            this.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            this.lineTo(x + radius, y + height);
            this.quadraticCurveTo(x, y + height, x, y + height - radius);
            this.lineTo(x, y + radius);
            this.quadraticCurveTo(x, y, x + radius, y);
            this.closePath();
        };
    }

    // Add CCTV markers
    cctvLayer = new ol.layer.Vector({
        source: new ol.source.Vector(),
        style: function(feature) {
            // Check site filter
            if (currentSiteFilter) {
                const cctvData = feature.get('cctvData');
                if (cctvData) {
                    const cctvSite = cctvData.site || null;
                    if (cctvSite !== currentSiteFilter) {
                        return null; // Hide feature if doesn't match filter
                    }
                } else {
                    return null; // Hide if no data
                }
            }
            
            const cctv = feature.get('cctvData');
            
            // Determine CCTV status (baik atau tidak baik)
            const kondisi = (cctv?.kondisi || cctv?.status || '').toString().toLowerCase();
            const status = (cctv?.status || '').toString().toLowerCase();
            const connected = (cctv?.connected || '').toString().toLowerCase();
            
            // CCTV dianggap baik jika: kondisi = 'baik' atau 'online', status = 'live view', connected = 'yes'
            const isGood = 
                kondisi === 'baik' || 
                kondisi === 'online' ||
                status === 'baik' ||
                status === 'live view' ||
                connected === 'yes' ||
                cctv?.status === 1 || 
                cctv?.status_online === 1 ||
                cctv?.is_online === true;
            
            // Determine color based on status
            let fillColor = "#a142f4"; // Default purple
            if (isGood) {
                fillColor = "#12b76a"; // Green untuk baik/online
            } else {
                fillColor = "#dc2626"; // Red untuk rusak/tidak baik (akan blink)
            }
            
            const iconUrl = createCCTVIcon({ 
                fill: fillColor, 
                live: isGood,
                isGood: isGood
            });
            
            // Calculate blink opacity untuk CCTV yang rusak (tidak baik)
            // CCTV rusak akan blink dengan opacity berubah dari 0.65 ke 1.0
            let iconOpacity = 1;
            if (!isGood && pulseAnimationStartTime !== null) {
                const blinkTime = getPulseAnimationTime();
                const cycle = 500; // 0.5 second cycle untuk blink cepat
                const progress = (blinkTime % cycle) / cycle;
                // Blink: opacity dari 0.65 ke 1.0 dan kembali (smooth sine wave)
                const sineWave = Math.sin(progress * Math.PI * 2);
                iconOpacity = 0.65 + (0.35 * (1 + sineWave) / 2); // dari 0.65 ke 1.0
            }
            
            // Create style dengan opacity yang berubah untuk animasi blink
            // Gunakan timestamp kecil untuk memaksa re-render setiap frame
            const styleKey = isGood ? 'good' : 'bad';
            const timeKey = pulseAnimationStartTime ? Math.floor(getPulseAnimationTime() / 50) : 0; // Update setiap 50ms
            
            return new ol.style.Style({
                image: new ol.style.Icon({
                    src: iconUrl,
                    scale: 1.0,  // Full scale for Google Maps style
                    anchor: [0.5, 1],  // Anchor at bottom center (pin point)
                    anchorXUnits: 'fraction',
                    anchorYUnits: 'fraction',
                    opacity: iconOpacity,
                    // Add rotation kecil untuk memaksa re-render (tidak terlihat)
                    rotation: timeKey * 0.0001
                })
            });
        },
        zIndex: 1001  // Z-index lebih tinggi dari hazard layer
    });
    map.addLayer(cctvLayer);

    // Helper function to add CCTV markers from any data array
    function addCctvMarkersFromData(cctvDataArray) {
        if (!cctvDataArray || cctvDataArray.length === 0) return;
        
        const source = cctvLayer.getSource();
        const features = [];
        const BATCH_SIZE = 100;
        
        // Prepare all features first
        cctvDataArray.forEach(function(cctv) {
            if (!cctv.location || !Array.isArray(cctv.location) || cctv.location.length !== 2) {
                return;
            }
            
            const feature = new ol.Feature({
                geometry: new ol.geom.Point(
                    ol.proj.fromLonLat(cctv.location)
                ),
                name: cctv.name || cctv.cctv_name || cctv.nama_cctv || 'CCTV',
                type: 'cctv',
                cctvData: cctv
            });
            features.push(feature);
        });
        
        // Batch add features
        if (features.length > 0) {
            let index = 0;
            function addBatch() {
                const batch = features.slice(index, index + BATCH_SIZE);
                if (batch.length > 0) {
                    source.addFeatures(batch);
                    index += BATCH_SIZE;
                    if (index < features.length) {
                        requestAnimationFrame(addBatch);
                    } else {
                        console.log(`Added ${features.length} CCTV markers in batches`);
                    }
                }
            }
            requestAnimationFrame(addBatch);
        }
    }
    
    // Gunakan cctvLocationsForMap untuk map marker (hanya yang punya koordinat)
    // Sidebar menggunakan cctvLocations (semua data termasuk yang tidak punya koordinat)
    // OPTIMIZED: Batch rendering untuk CCTV markers
    function addCctvMarkersBatch() {
        if (!cctvLocationsForMap || cctvLocationsForMap.length === 0) return;
        addCctvMarkersFromData(cctvLocationsForMap);
    }
    
    // Defer CCTV marker rendering
    setTimeout(() => {
        addCctvMarkersBatch();
    }, 300);

    // OPTIMIZED: Batch rendering untuk insiden markers
    function addInsidenMarkersBatch() {
        if (!insidenDataset || insidenDataset.length === 0) return;
        
        const source = insidenLayer.getSource();
        const features = [];
        const BATCH_SIZE = 100;
        
        insidenDataset.forEach(function (insiden) {
            if (!insiden.latitude || !insiden.longitude) return;
            
            const feature = new ol.Feature({
                geometry: new ol.geom.Point(
                    ol.proj.fromLonLat([parseFloat(insiden.longitude), parseFloat(insiden.latitude)])
                ),
                type: 'insiden',
                insidenId: insiden.no_kecelakaan,
                data: insiden
            });
            features.push(feature);
        });
        
        if (features.length > 0) {
            let index = 0;
            function addBatch() {
                const batch = features.slice(index, index + BATCH_SIZE);
                if (batch.length > 0) {
                    source.addFeatures(batch);
                    index += BATCH_SIZE;
                    if (index < features.length) {
                        requestAnimationFrame(addBatch);
                    }
                }
            }
            requestAnimationFrame(addBatch);
        }
    }
    
    // Defer insiden marker rendering
    setTimeout(() => {
        addInsidenMarkersBatch();
    }, 400);

    // Create vector layer for GR (Golden Rule)
    grLayer = new ol.layer.Vector({
        source: new ol.source.Vector(),
        style: function(feature) {
            // Check site filter
            if (currentSiteFilter) {
                const grData = feature.get('data');
                if (grData) {
                    const grSite = grData.site || null;
                    if (grSite !== currentSiteFilter) {
                        return null; // Hide feature if doesn't match filter
                    }
                } else {
                    return null; // Hide if no data
                }
            }
            
            return new ol.style.Style({
                image: new ol.style.Circle({
                    radius: 8,
                    fill: new ol.style.Fill({ color: '#8b5cf6' }), // Purple for GR
                    stroke: new ol.style.Stroke({
                        color: '#ffffff',
                        width: 2
                    })
                })
            });
        },
        zIndex: 1002  // Z-index untuk GR layer
    });
    map.addLayer(grLayer);

    // OPTIMIZED: Batch rendering untuk GR markers
    function addGrMarkersBatch() {
        if (!grDetections || !Array.isArray(grDetections)) return;
        
        const source = grLayer.getSource();
        const features = [];
        const BATCH_SIZE = 100;
        
        grDetections.forEach(function(gr) {
            if (gr.location && gr.location.lat && gr.location.lng) {
                const feature = new ol.Feature({
                    geometry: new ol.geom.Point(
                        ol.proj.fromLonLat([gr.location.lng, gr.location.lat])
                    ),
                    type: 'gr',
                    grId: gr.id,
                    data: gr
                });
                features.push(feature);
            }
        });
        
        if (features.length > 0) {
            let index = 0;
            function addBatch() {
                const batch = features.slice(index, index + BATCH_SIZE);
                if (batch.length > 0) {
                    source.addFeatures(batch);
                    index += BATCH_SIZE;
                    if (index < features.length) {
                        requestAnimationFrame(addBatch);
                    }
                }
            }
            requestAnimationFrame(addBatch);
        }
    }
    
    // Defer GR marker rendering
    setTimeout(() => {
        addGrMarkersBatch();
    }, 600);

    // Function to create vehicle unit icon (mirip dengan CCTV icon style)
    function createVehicleUnitIcon(unit) {
        // Determine color based on vehicle type
        let fillColor = '#3b82f6'; // Default blue
        if (unit.vehicle_type) {
            const vehicleType = unit.vehicle_type.toLowerCase();
            if (vehicleType.includes('dump') || vehicleType.includes('truck')) {
                fillColor = '#f59e0b'; // Orange for trucks
            } else if (vehicleType.includes('prime') || vehicleType.includes('mover')) {
                fillColor = '#10b981'; // Green for prime movers
            } else if (vehicleType.includes('lube')) {
                fillColor = '#8b5cf6'; // Purple for lube trucks
            }
        }
        
        // Use same style as CCTV icon but with vehicle glyph
        const size = 56;
        const W = size, H = size;
        const dpr = Math.max(1, Math.min(3, window.devicePixelRatio || 1));

        const c = document.createElement("canvas");
        c.width = Math.round(W * dpr);
        c.height = Math.round(H * dpr);
        const ctx = c.getContext("2d");
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, W, H);

        // Geometry (proporsional ke size) - sama dengan CCTV
        const cx = W * 0.5;
        const cy = H * 0.40;
        const R_outer = W * 0.37;  // outer white ring shape radius
        const R_inner = W * 0.30;  // inner colored circle radius
        const tailH  = H * 0.22;
        const tailW  = W * 0.30;

        // Helper functions (sama dengan CCTV)
        function bubblePath(R) {
            const baseY = cy + R * 0.78;
            const dy = baseY - cy;
            const dx = Math.sqrt(Math.max(0, R * R - dy * dy));
            const xL = cx - dx, xR = cx + dx;
            const aL = Math.atan2(dy, -dx);
            const aR = Math.atan2(dy,  dx);

            ctx.beginPath();
            ctx.moveTo(xL, baseY);
            ctx.arc(cx, cy, R, aL, aR, true);

            const half = tailW / 2;
            const tipX = cx;
            const tipY = baseY + tailH;

            ctx.quadraticCurveTo(cx + half, baseY + 2, cx + half * 0.72, baseY + tailH * 0.55);
            ctx.quadraticCurveTo(cx + half * 0.26, baseY + tailH * 0.92, tipX, tipY);
            ctx.quadraticCurveTo(cx - half * 0.26, baseY + tailH * 0.92, cx - half * 0.72, baseY + tailH * 0.55);
            ctx.quadraticCurveTo(cx - half, baseY + 2, xL, baseY);

            ctx.closePath();
        }

        function roundRect(x, y, w, h, r) {
            r = Math.min(r, w / 2, h / 2);
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.arcTo(x + w, y, x + w, y + h, r);
            ctx.arcTo(x + w, y + h, x, y + h, r);
            ctx.arcTo(x, y + h, x, y, r);
            ctx.arcTo(x, y, x + w, y, r);
            ctx.closePath();
        }

        function shade(hex, amt) {
            const c = hex.replace("#", "");
            const n = parseInt(c.length === 3 ? c.split("").map(x => x + x).join("") : c, 16);
            let r = (n >> 16) & 255, g = (n >> 8) & 255, b = n & 255;
            r = Math.max(0, Math.min(255, Math.round(r + 255 * amt)));
            g = Math.max(0, Math.min(255, Math.round(g + 255 * amt)));
            b = Math.max(0, Math.min(255, Math.round(b + 255 * amt)));
            return `rgb(${r},${g},${b})`;
        }

        // ---------- draw shadow ----------
        ctx.save();
        ctx.shadowColor = "rgba(0,0,0,.30)";
        ctx.shadowBlur = Math.max(6, W * 0.14);
        ctx.shadowOffsetY = Math.max(2, H * 0.06);
        bubblePath(R_outer);
        ctx.fillStyle = "#fff";
        ctx.fill();
        ctx.restore();

        // ---------- outer white ring ----------
        ctx.save();
        bubblePath(R_outer);
        ctx.fillStyle = "#fff";
        ctx.fill();
        ctx.restore();

        // subtle border stroke
        ctx.save();
        bubblePath(R_outer);
        ctx.lineWidth = Math.max(1, W * 0.02);
        ctx.strokeStyle = "rgba(0,0,0,.06)";
        ctx.stroke();
        ctx.restore();

        // ---------- inner colored circle (gradient + gloss) ----------
        ctx.save();
        const g = ctx.createRadialGradient(cx - R_inner * 0.35, cy - R_inner * 0.35, R_inner * 0.15, cx, cy, R_inner);
        g.addColorStop(0.00, shade(fillColor, +0.22));
        g.addColorStop(0.55, fillColor);
        g.addColorStop(1.00, shade(fillColor, -0.18));

        ctx.beginPath();
        ctx.arc(cx, cy, R_inner, 0, Math.PI * 2);
        ctx.fillStyle = g;
        ctx.fill();

        // inner ring highlight
        ctx.lineWidth = Math.max(1.2, W * 0.025);
        ctx.strokeStyle = "rgba(255,255,255,.35)";
        ctx.stroke();

        // gloss highlight
        ctx.globalCompositeOperation = "screen";
        ctx.beginPath();
        ctx.ellipse(cx - R_inner * 0.18, cy - R_inner * 0.22, R_inner * 0.72, R_inner * 0.58, -0.2, 0, Math.PI * 2);
        const gloss = ctx.createRadialGradient(cx - R_inner * 0.25, cy - R_inner * 0.35, 0, cx, cy, R_inner);
        gloss.addColorStop(0, "rgba(255,255,255,.40)");
        gloss.addColorStop(1, "rgba(255,255,255,0)");
        ctx.fillStyle = gloss;
        ctx.fill();
        ctx.globalCompositeOperation = "source-over";

        ctx.restore();

        // ---------- glyph (truck/vehicle icon, putih) ----------
        ctx.save();
        ctx.translate(cx, cy);
        ctx.fillStyle = "#fff";

        // Vehicle body (main rectangle)
        const vw = W * 0.32, vh = H * 0.16;
        roundRect(-vw / 2, -vh / 2 + H * 0.02, vw, vh, W * 0.04);
        ctx.fill();

        // Vehicle cabin (smaller rectangle on left)
        const cw = W * 0.18, ch = H * 0.14;
        roundRect(-vw / 2 - cw * 0.1, -ch / 2 + H * 0.02, cw, ch, W * 0.03);
        ctx.fill();

        // Window (dark rectangle in cabin)
        ctx.fillStyle = "rgba(30, 41, 59, 0.7)";
        roundRect(-vw / 2 - cw * 0.05, -ch / 2 + H * 0.025, cw * 0.5, ch * 0.5, W * 0.015);
        ctx.fill();

        // Wheels (two circles)
        ctx.fillStyle = "rgba(15, 23, 42, 0.8)";
        ctx.beginPath();
        ctx.arc(-vw / 2 + vw * 0.25, vh / 2 + H * 0.02, W * 0.05, 0, Math.PI * 2);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(vw / 2 - vw * 0.25, vh / 2 + H * 0.02, W * 0.05, 0, Math.PI * 2);
        ctx.fill();

        // Wheel rims (white circles inside)
        ctx.fillStyle = "rgba(255, 255, 255, 0.6)";
        ctx.beginPath();
        ctx.arc(-vw / 2 + vw * 0.25, vh / 2 + H * 0.02, W * 0.025, 0, Math.PI * 2);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(vw / 2 - vw * 0.25, vh / 2 + H * 0.02, W * 0.025, 0, Math.PI * 2);
        ctx.fill();

        ctx.restore();

        return c.toDataURL("image/png");
    }
    
    // Create vector layer for unit vehicles
    unitVehicleLayer = new ol.layer.Vector({
        source: new ol.source.Vector(),
        visible: false,  // Default hidden untuk performa
        style: function(feature) {
            const unit = feature.get('unitData');
            if (!unit) {
                return null; // Hide if no unit data
            }
            
            const iconUrl = createVehicleUnitIcon(unit);
            const course = parseFloat(unit.course) || 0;
            
            return new ol.style.Style({
                image: new ol.style.Icon({
                    src: iconUrl,
                    scale: 1.0,  // Full scale like CCTV
                    anchor: [0.5, 1],  // Anchor at bottom center (pin point) - same as CCTV
                    anchorXUnits: 'fraction',
                    anchorYUnits: 'fraction',
                    opacity: 1,
                    rotation: course * Math.PI / 180 // Rotate based on course (convert degrees to radians)
                })
            });
        },
        zIndex: 1002  // Z-index above CCTV layer
    });
    map.addLayer(unitVehicleLayer);
    console.log('Unit vehicle layer created and added to map');

    // Function to add/update unit vehicle markers
    function updateUnitVehicleMarkers(units) {
        if (!unitVehicleLayer) {
            console.warn('Unit vehicle layer not initialized');
            return;
        }
        
        const source = unitVehicleLayer.getSource();
        const existingFeatures = source.getFeatures();
        const existingUnitsMap = new Map();
        
        // Create map of existing features by unitId
        existingFeatures.forEach(function(feature) {
            const unitId = feature.get('unitId');
            if (unitId) {
                existingUnitsMap.set(unitId, feature);
            }
        });
        
        // Process new/updated units
        const processedUnitIds = new Set();
        let addedCount = 0;
        let updatedCount = 0;
        let skippedCount = 0;
        
        units.forEach(function(unit) {
            // Validate coordinates
            if (!unit.latitude || !unit.longitude || 
                unit.latitude === 0 || unit.longitude === 0 ||
                isNaN(parseFloat(unit.latitude)) || isNaN(parseFloat(unit.longitude))) {
                skippedCount++;
                return;
            }
            
            const unitId = unit.unit_id || unit.id || unit.integration_id;
            if (!unitId) {
                skippedCount++;
                return;
            }
            
            processedUnitIds.add(unitId);
            
            // Convert coordinates to map projection
            const longitude = parseFloat(unit.longitude);
            const latitude = parseFloat(unit.latitude);
            const coordinate = ol.proj.fromLonLat([longitude, latitude]);
            
            // Check if feature already exists
            if (existingUnitsMap.has(unitId)) {
                // Update existing feature
                const feature = existingUnitsMap.get(unitId);
                const oldCoord = feature.getGeometry().getCoordinates();
                
                // Only update if coordinates changed
                if (oldCoord[0] !== coordinate[0] || oldCoord[1] !== coordinate[1]) {
                    feature.getGeometry().setCoordinates(coordinate);
                    updatedCount++;
                }
                
                // Always update unit data (for other properties like speed, battery, etc.)
                feature.set('unitData', unit);
                feature.set('unitId', unitId);
                
                // Trigger style update for icon rotation based on course
                feature.changed();
            } else {
                // Create new feature
                const feature = new ol.Feature({
                    geometry: new ol.geom.Point(coordinate),
                    type: 'unit_vehicle',
                    unitId: unitId,
                    unitData: unit
                });
                source.addFeature(feature);
                addedCount++;
            }
        });
        
        // Remove features that no longer exist in the data
        let removedCount = 0;
        existingFeatures.forEach(function(feature) {
            const unitId = feature.get('unitId');
            if (unitId && !processedUnitIds.has(unitId)) {
                source.removeFeature(feature);
                removedCount++;
            }
        });
        
        console.log('Unit vehicles updated:', {
            total: processedUnitIds.size,
            added: addedCount,
            updated: updatedCount,
            removed: removedCount,
            skipped: skippedCount
        });
    }

    // Initial load of unit vehicles
    console.log('Loading unit vehicles:', unitVehicles.length, 'units');
    updateUnitVehicleMarkers(unitVehicles);
    // Update filteredSidebarData.unit dengan data initial
    if (unitVehicles && unitVehicles.length > 0) {
        filteredSidebarData.unit = [...unitVehicles];
        updateTabCounts();
    }
    
    // Function to create GPS Orang icon (mirip dengan CCTV icon style)
    function createGpsOrangIcon({
        fill = "#3b82f6",  // Default blue
        size = 56
    } = {}) {
        const W = size, H = size;
        const dpr = Math.max(1, Math.min(3, window.devicePixelRatio || 1));

        const c = document.createElement("canvas");
        c.width = Math.round(W * dpr);
        c.height = Math.round(H * dpr);
        const ctx = c.getContext("2d");
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, W, H);

        // Geometry (proporsional ke size) - sama dengan CCTV
        const cx = W * 0.5;
        const cy = H * 0.40;
        const R_outer = W * 0.37;
        const R_inner = W * 0.30;
        const tailH  = H * 0.22;
        const tailW  = W * 0.30;

        // Helper functions
        function bubblePath(R) {
            const baseY = cy + R * 0.78;
            const dy = baseY - cy;
            const dx = Math.sqrt(Math.max(0, R * R - dy * dy));
            const xL = cx - dx, xR = cx + dx;
            const aL = Math.atan2(dy, -dx);
            const aR = Math.atan2(dy,  dx);

            ctx.beginPath();
            ctx.moveTo(xL, baseY);
            ctx.arc(cx, cy, R, aL, aR, true);

            const half = tailW / 2;
            const tipX = cx;
            const tipY = baseY + tailH;

            ctx.quadraticCurveTo(cx + half, baseY + 2, cx + half * 0.72, baseY + tailH * 0.55);
            ctx.quadraticCurveTo(cx + half * 0.26, baseY + tailH * 0.92, tipX, tipY);
            ctx.quadraticCurveTo(cx - half * 0.26, baseY + tailH * 0.92, cx - half * 0.72, baseY + tailH * 0.55);
            ctx.quadraticCurveTo(cx - half, baseY + 2, xL, baseY);

            ctx.closePath();
        }

        function roundRect(x, y, w, h, r) {
            r = Math.min(r, w / 2, h / 2);
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.arcTo(x + w, y, x + w, y + h, r);
            ctx.arcTo(x + w, y + h, x, y + h, r);
            ctx.arcTo(x, y + h, x, y, r);
            ctx.arcTo(x, y, x + w, y, r);
            ctx.closePath();
        }

        function shade(hex, amt) {
            const c = hex.replace("#", "");
            const n = parseInt(c.length === 3 ? c.split("").map(x => x + x).join("") : c, 16);
            let r = (n >> 16) & 255, g = (n >> 8) & 255, b = n & 255;
            r = Math.max(0, Math.min(255, Math.round(r + 255 * amt)));
            g = Math.max(0, Math.min(255, Math.round(g + 255 * amt)));
            b = Math.max(0, Math.min(255, Math.round(b + 255 * amt)));
            return `rgb(${r},${g},${b})`;
        }

        // ---------- draw shadow ----------
        ctx.save();
        ctx.shadowColor = "rgba(0,0,0,.30)";
        ctx.shadowBlur = Math.max(6, W * 0.14);
        ctx.shadowOffsetY = Math.max(2, H * 0.06);
        bubblePath(R_outer);
        ctx.fillStyle = "#fff";
        ctx.fill();
        ctx.restore();

        // ---------- outer white ring ----------
        ctx.save();
        bubblePath(R_outer);
        ctx.fillStyle = "#fff";
        ctx.fill();
        ctx.restore();

        // subtle border stroke
        ctx.save();
        bubblePath(R_outer);
        ctx.lineWidth = Math.max(1, W * 0.02);
        ctx.strokeStyle = "rgba(0,0,0,.06)";
        ctx.stroke();
        ctx.restore();

        // ---------- inner colored circle (gradient + gloss) ----------
        ctx.save();
        const g = ctx.createRadialGradient(cx - R_inner * 0.35, cy - R_inner * 0.35, R_inner * 0.15, cx, cy, R_inner);
        g.addColorStop(0.00, shade(fill, +0.22));
        g.addColorStop(0.55, fill);
        g.addColorStop(1.00, shade(fill, -0.18));

        ctx.beginPath();
        ctx.arc(cx, cy, R_inner, 0, Math.PI * 2);
        ctx.fillStyle = g;
        ctx.fill();

        // inner ring highlight
        ctx.lineWidth = Math.max(1.2, W * 0.025);
        ctx.strokeStyle = "rgba(255,255,255,.35)";
        ctx.stroke();

        // gloss highlight
        ctx.globalCompositeOperation = "screen";
        ctx.beginPath();
        ctx.ellipse(cx - R_inner * 0.18, cy - R_inner * 0.22, R_inner * 0.72, R_inner * 0.58, -0.2, 0, Math.PI * 2);
        const gloss = ctx.createRadialGradient(cx - R_inner * 0.25, cy - R_inner * 0.35, 0, cx, cy, R_inner);
        gloss.addColorStop(0, "rgba(255,255,255,.40)");
        gloss.addColorStop(1, "rgba(255,255,255,0)");
        ctx.fillStyle = gloss;
        ctx.fill();
        ctx.globalCompositeOperation = "source-over";

        ctx.restore();

        // ---------- glyph (person icon, putih) ----------
        ctx.save();
        ctx.translate(cx, cy);
        ctx.fillStyle = "#fff";

        // Head (circle)
        ctx.beginPath();
        ctx.arc(0, -H * 0.08, W * 0.08, 0, Math.PI * 2);
        ctx.fill();

        // Body (rounded rectangle)
        const bodyW = W * 0.24, bodyH = H * 0.20;
        roundRect(-bodyW / 2, H * 0.02, bodyW, bodyH, W * 0.04);
        ctx.fill();

        ctx.restore();

        return c.toDataURL("image/png");
    }
    
    // Create vector layer for GPS Orang
    userGpsLayer = new ol.layer.Vector({
        source: new ol.source.Vector(),
        visible: false,  // Default hidden
        style: function(feature) {
            const userData = feature.get('userData');
            if (!userData) {
                return null;
            }
            
            // Determine color based on battery level or default
            let fillColor = '#3b82f6'; // Default blue
            const battery = userData.battery;
            if (battery !== null && battery !== undefined) {
                if (battery < 20) {
                    fillColor = '#8b5cf6'; // Purple for low battery (instead of red)
                } else if (battery < 50) {
                    fillColor = '#f59e0b'; // Orange for medium battery
                } else {
                    fillColor = '#10b981'; // Green for good battery
                }
            }
            
            const iconUrl = createGpsOrangIcon({ fill: fillColor });
            
            return new ol.style.Style({
                image: new ol.style.Icon({
                    src: iconUrl,
                    scale: 1.0,
                    anchor: [0.5, 1],  // Anchor at bottom center (pin point)
                    anchorXUnits: 'fraction',
                    anchorYUnits: 'fraction',
                    opacity: 1
                })
            });
        },
        zIndex: 1002  // Z-index above CCTV layer
    });
    map.addLayer(userGpsLayer);
    console.log('GPS Orang layer created and added to map');
    
    // Function to add/update GPS Orang markers
    function updateGpsOrangMarkers(users) {
        if (!userGpsLayer) {
            console.warn('GPS Orang layer not initialized');
            return;
        }
        
        const source = userGpsLayer.getSource();
        const existingFeatures = source.getFeatures();
        const existingUsersMap = new Map();
        
        // Create map of existing features by userId
        existingFeatures.forEach(function(feature) {
            const userId = feature.get('userId');
            if (userId) {
                existingUsersMap.set(userId, feature);
            }
        });
        
        // Process new/updated users
        const processedUserIds = new Set();
        let addedCount = 0;
        let updatedCount = 0;
        let skippedCount = 0;
        
        users.forEach(function(user) {
            // Validate coordinates
            if (!user.latitude || !user.longitude || 
                user.latitude === 0 || user.longitude === 0 ||
                isNaN(parseFloat(user.latitude)) || isNaN(parseFloat(user.longitude))) {
                skippedCount++;
                return;
            }
            
            const userId = user.user_id || user.id;
            if (!userId) {
                skippedCount++;
                return;
            }
            
            processedUserIds.add(userId);
            
            // Convert coordinates to map projection
            const longitude = parseFloat(user.longitude);
            const latitude = parseFloat(user.latitude);
            const coordinate = ol.proj.fromLonLat([longitude, latitude]);
            
            // Check if feature already exists
            if (existingUsersMap.has(userId)) {
                const feature = existingUsersMap.get(userId);
                const oldCoord = feature.getGeometry().getCoordinates();
                
                // Only update if coordinates changed
                if (oldCoord[0] !== coordinate[0] || oldCoord[1] !== coordinate[1]) {
                    feature.getGeometry().setCoordinates(coordinate);
                    updatedCount++;
                }
                
                // Always update user data
                feature.set('userData', user);
                feature.set('userId', userId);
                feature.changed();
            } else {
                // Create new feature
                const feature = new ol.Feature({
                    geometry: new ol.geom.Point(coordinate),
                    type: 'gps_orang',
                    userId: userId,
                    userData: user
                });
                source.addFeature(feature);
                addedCount++;
            }
        });
        
        // Remove features that no longer exist in the data
        let removedCount = 0;
        existingFeatures.forEach(function(feature) {
            const userId = feature.get('userId');
            if (userId && !processedUserIds.has(userId)) {
                source.removeFeature(feature);
                removedCount++;
            }
        });
        
        console.log('GPS Orang updated:', {
            total: processedUserIds.size,
            added: addedCount,
            updated: updatedCount,
            removed: removedCount,
            skipped: skippedCount
        });
    }
    
    // Function to refresh unit vehicle data from server
    async function refreshUnitVehicles() {
        try {
            const response = await fetch('{{ route("maps.api.unit-vehicles") }}');
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.unitVehicles) {
                    // Update global unitVehicles array
                    unitVehicles = data.unitVehicles;
                    
                    // Update filteredSidebarData.unit untuk sidebar
                    filteredSidebarData.unit = [...(data.unitVehicles || [])];
                    
                    // Update markers on map
                    updateUnitVehicleMarkers(data.unitVehicles);
                    
                    // Update tab count
                    updateTabCounts();
                    
                    // Update unit list di sidebar jika tab unit aktif
                    if (currentSidebarTab === 'unit') {
                        renderUnitList(filteredSidebarData.unit);
                    } else {
                        // Jika tab unit tidak aktif, pastikan data sudah siap untuk ditampilkan saat user klik tab unit
                        console.log('Unit data loaded, ready for sidebar:', filteredSidebarData.unit.length, 'units');
                        // Update tab count untuk menampilkan jumlah unit yang benar
                        updateTabCounts();
                    }
                    
                    // Update unit list if unit view is active (untuk view selector lama)
                    const viewSelector = document.getElementById('viewSelector');
                    if (viewSelector && viewSelector.value === 'unit') {
                        if (typeof window.renderUnitList === 'function') {
                            window.renderUnitList();
                        }
                    }
                    
                    console.log('Unit vehicles refreshed:', data.count, 'units at', new Date().toLocaleTimeString());
                    console.log('Unit data sample:', filteredSidebarData.unit.slice(0, 3));
                } else {
                    console.warn('Failed to refresh unit vehicles:', data);
                }
            } else {
                console.error('Failed to fetch unit vehicles:', response.status, response.statusText);
            }
        } catch (error) {
            console.error('Error refreshing unit vehicles:', error);
        }
    }
    
    // Refresh unit vehicles immediately on page load
    // Tunggu sedikit untuk memastikan semua fungsi sudah terdefinisi
    setTimeout(() => {
        refreshUnitVehicles();
    }, 500);
    
    // Auto-refresh unit vehicles every 30 seconds
    let unitVehicleRefreshInterval = setInterval(refreshUnitVehicles, 30000);
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (unitVehicleRefreshInterval) {
            clearInterval(unitVehicleRefreshInterval);
        }
    });
    
    // Expose refresh function globally for manual refresh if needed
    window.refreshUnitVehicles = refreshUnitVehicles;

    // Load BMO2 PAMA layers after map is created
    setTimeout(function() {
        console.log('Loading BMO2 PAMA layers...');
        console.log('Checking available variables:');
        console.log('- areaKerjaGeoJsonDataPama:', typeof window.areaKerjaGeoJsonDataPama);
        console.log('- areaCctvGeoJsonDataBmo2Pama:', typeof window.areaCctvGeoJsonDataBmo2Pama);
        console.log('- difference_bmo2_pama:', typeof window.difference_bmo2_pama);
        console.log('- symmetrical_difference_bmo1_fad:', typeof window.symmetrical_difference_bmo1_fad);
        console.log('- intersection_bmo1_fad:', typeof window.intersection_bmo1_fad);

        // Area Kerja BMO2 PAMA
        // Note: Using createLayerFromGeoJson32650 because coordinates are in EPSG:32650 (UTM Zone 50N)
        // even though CRS in file is declared as CRS84
        if (typeof window.areaKerjaGeoJsonDataPama !== 'undefined' && window.areaKerjaGeoJsonDataPama) {
            try {
                console.log('Loading Area Kerja BMO2 PAMA with EPSG:32650 transformation...');
                areaKerjaBmo2PamaLayer = createLayerFromGeoJson32650(
                    window.areaKerjaGeoJsonDataPama,
                    'Area Kerja BMO2 PAMA',
                    getAreaKerjaStyle,
                    410
                );
                // Ensure layer is visible
                if (areaKerjaBmo2PamaLayer) {
                    areaKerjaBmo2PamaLayer.setVisible(true);
                    areaKerjaBmo2PamaLayer.setOpacity(1.0);
                    map.addLayer(areaKerjaBmo2PamaLayer);
                    const featureCount = areaKerjaBmo2PamaLayer.getSource().getFeatures().length;
                    console.log('✓ Area Kerja BMO2 PAMA layer added, features:', featureCount);
                    console.log('✓ Area Kerja BMO2 PAMA layer visible:', areaKerjaBmo2PamaLayer.getVisible());
                    console.log('✓ Area Kerja BMO2 PAMA layer opacity:', areaKerjaBmo2PamaLayer.getOpacity());
                    
                    // Log extent to verify layer is loaded correctly
                    const extent = areaKerjaBmo2PamaLayer.getSource().getExtent();
                    console.log('✓ Area Kerja BMO2 PAMA extent:', extent);
                } else {
                    console.error('✗ Failed to create Area Kerja BMO2 PAMA layer');
                }
            } catch (error) {
                console.error('Error creating Area Kerja BMO2 PAMA layer:', error);
                console.error('Error stack:', error.stack);
            }
        } else {
            console.warn('✗ areaKerjaGeoJsonDataPama not found or undefined');
        }

        // Area CCTV BMO2 PAMA
        if (typeof window.areaCctvGeoJsonDataBmo2Pama !== 'undefined' && window.areaCctvGeoJsonDataBmo2Pama) {
            try {
                areaCctvBmo2PamaLayer = createLayerFromGeoJson(
                    window.areaCctvGeoJsonDataBmo2Pama,
                    'Area CCTV BMO2 PAMA',
                    getAreaCctvStyle,
                    510
                );
                // Ensure layer is visible
                areaCctvBmo2PamaLayer.setVisible(true);
                map.addLayer(areaCctvBmo2PamaLayer);
                console.log('✓ Area CCTV BMO2 PAMA layer added, features:', areaCctvBmo2PamaLayer.getSource().getFeatures().length);
                console.log('✓ Area CCTV BMO2 PAMA layer visible:', areaCctvBmo2PamaLayer.getVisible());
            } catch (error) {
                console.error('Error creating Area CCTV BMO2 PAMA layer:', error);
            }
        } else {
            console.warn('✗ areaCctvGeoJsonDataBmo2Pama not found or undefined');
        }

        // Difference BMO2 PAMA
        if (typeof window.difference_bmo2_pama !== 'undefined' && window.difference_bmo2_pama) {
            try {
                differenceBmo2PamaLayer = createLayerFromGeoJson(
                    window.difference_bmo2_pama,
                    'Difference BMO2 PAMA',
                    getDifferenceStyle,
                    350
                );
                differenceBmo2PamaLayer.setVisible(true);
                map.addLayer(differenceBmo2PamaLayer);
                console.log('✓ Difference BMO2 PAMA layer added, features:', differenceBmo2PamaLayer.getSource().getFeatures().length);
            } catch (error) {
                console.error('Error creating Difference BMO2 PAMA layer:', error);
            }
        } else {
            console.warn('✗ difference_bmo2_pama not found or undefined');
        }

        // Symmetrical Difference BMO2 PAMA
        if (typeof window.symmetrical_difference_bmo1_fad !== 'undefined' && window.symmetrical_difference_bmo1_fad) {
            try {
                // Note: Variable name is wrong in file, but data is for BMO2 PAMA
                symmetricalDifferenceBmo2PamaLayer = createLayerFromGeoJson(
                    window.symmetrical_difference_bmo1_fad,
                    'Symmetrical Difference BMO2 PAMA',
                    getSymmetricalDifferenceStyle,
                    360
                );
                symmetricalDifferenceBmo2PamaLayer.setVisible(true);
                map.addLayer(symmetricalDifferenceBmo2PamaLayer);
                console.log('✓ Symmetrical Difference BMO2 PAMA layer added, features:', symmetricalDifferenceBmo2PamaLayer.getSource().getFeatures().length);
            } catch (error) {
                console.error('Error creating Symmetrical Difference BMO2 PAMA layer:', error);
            }
        } else {
            console.warn('✗ symmetrical_difference_bmo1_fad not found or undefined');
        }

        // Intersection BMO2 PAMA
        if (typeof window.intersection_bmo1_fad !== 'undefined' && window.intersection_bmo1_fad) {
            try {
                // Note: Variable name is wrong in file, but data is for BMO2 PAMA
                intersectionBmo2PamaLayer = createLayerFromGeoJson(
                    window.intersection_bmo1_fad,
                    'Intersection BMO2 PAMA',
                    getIntersectionStyle,
                    370
                );
                intersectionBmo2PamaLayer.setVisible(true);
                map.addLayer(intersectionBmo2PamaLayer);
                console.log('✓ Intersection BMO2 PAMA layer added, features:', intersectionBmo2PamaLayer.getSource().getFeatures().length);
            } catch (error) {
                console.error('Error creating Intersection BMO2 PAMA layer:', error);
            }
        } else {
            console.warn('✗ intersection_bmo1_fad not found or undefined');
        }
        
        // Ensure all area kerja layers are visible by default
        if (areaKerjaBmo2PamaLayer) {
            areaKerjaBmo2PamaLayer.setVisible(true);
            console.log('✓ Area Kerja BMO2 PAMA layer set to visible');
        }
        if (areaCctvBmo2PamaLayer) {
            areaCctvBmo2PamaLayer.setVisible(true);
            console.log('✓ Area CCTV BMO2 PAMA layer set to visible');
        }
        if (differenceBmo2PamaLayer) {
            differenceBmo2PamaLayer.setVisible(true);
        }
        if (symmetricalDifferenceBmo2PamaLayer) {
            symmetricalDifferenceBmo2PamaLayer.setVisible(true);
        }
        if (intersectionBmo2PamaLayer) {
            intersectionBmo2PamaLayer.setVisible(true);
        }
        
        console.log('Finished loading BMO2 PAMA layers - All area kerja layers are visible');

        if (areaCctvBmo2PamaLayer && intersectionBmo2PamaLayer) {
            try {
                populateCoverageTable();
            } catch (error) {
                console.error('Error populating coverage table:', error);
            }
        }
        
        // Load all Area CCTV and Area Kerja layers
        // Wait for proj4js to be available
        function loadAllAreaLayers() {
            if (typeof proj4 === 'undefined') {
                console.warn('proj4js not loaded yet, retrying in 200ms...');
                setTimeout(loadAllAreaLayers, 200);
                return;
            }
            
            // Verify OpenLayers is available
            if (typeof ol === 'undefined' || typeof ol.proj === 'undefined') {
                console.warn('OpenLayers not loaded yet, retrying in 200ms...');
                setTimeout(loadAllAreaLayers, 200);
                return;
            }
            
            // Verify map is available
            if (typeof map === 'undefined' || !map) {
                console.warn('Map not available yet, retrying in 200ms...');
                setTimeout(loadAllAreaLayers, 200);
                return;
            }
            
            console.log('proj4, OpenLayers, and map are available, starting to load layers...');
            console.log('Loading all Area CCTV and Area Kerja layers...');
            
            // Store all layers in arrays for easy management
            const areaCctvLayers = [];
            const areaKerjaLayers = [];
            
            // Area CCTV Layers
            const areaCctvConfigs = [
                { varName: 'areaCctvBmo1Fad', layerName: 'Area CCTV BMO1 FAD', zIndex: 511 },
                { varName: 'areaCctvBmo1Kdc', layerName: 'Area CCTV BMO1 KDC', zIndex: 512 },
                { varName: 'areaCctvBmo2Buma', layerName: 'Area CCTV BMO2 BUMA', zIndex: 513 },
                { varName: 'areaCctvBmo2Pama', layerName: 'Area CCTV BMO2 PAMA', zIndex: 513 },
                { varName: 'areaCctvBmo3', layerName: 'Area CCTV BMO3 BAR', zIndex: 514 },
                { varName: 'areaCctvGmoKdc', layerName: 'Area CCTV GMO KDC', zIndex: 515 },
                { varName: 'areaCctvGmoPama', layerName: 'Area CCTV GMO PAMA', zIndex: 516 },
                { varName: 'areaCctvLmoBuma', layerName: 'Area CCTV LMO BUMA', zIndex: 517 },
                { varName: 'areaCctvLmoFad', layerName: 'Area CCTV LMO FAD', zIndex: 517 },
                { varName: 'areaCctvSmoMtn', layerName: 'Area CCTV SMO MTN', zIndex: 518 }
            ];
            
            areaCctvConfigs.forEach(config => {
                if (typeof window[config.varName] !== 'undefined' && window[config.varName]) {
                    try {
                        console.log(`Loading ${config.layerName}...`);
                        console.log(`Data check - ${config.varName}:`, {
                            hasData: !!window[config.varName],
                            featuresCount: window[config.varName].features ? window[config.varName].features.length : 0,
                            crs: window[config.varName].crs
                        });
                        const layer = createLayerFromGeoJson32650(
                            window[config.varName],
                            config.layerName,
                            getAreaCctvStyle,
                            config.zIndex
                        );
                        if (layer) {
                            const featureCount = layer.getSource().getFeatures().length;
                            console.log(`Layer created for ${config.layerName}, features:`, featureCount);
                            
                            if (featureCount > 0) {
                                layer.setVisible(true);
                                layer.setOpacity(1.0);
                                layer.setZIndex(config.zIndex);
                                map.addLayer(layer);
                                areaCctvLayers.push(layer);
                                
                                // Log layer details
                                const extent = layer.getSource().getExtent();
                                console.log(`✓ ${config.layerName} layer added successfully:`, {
                                    features: featureCount,
                                    visible: layer.getVisible(),
                                    opacity: layer.getOpacity(),
                                    zIndex: layer.getZIndex(),
                                    extent: extent
                                });
                            } else {
                                console.warn(`⚠ ${config.layerName} layer created but has no features`);
                            }
                        } else {
                            console.error(`✗ Failed to create layer for ${config.layerName}`);
                        }
                    } catch (error) {
                        console.error(`Error creating ${config.layerName} layer:`, error);
                        console.error('Error stack:', error.stack);
                        console.error('Data sample:', JSON.stringify(window[config.varName]).substring(0, 500));
                    }
                } else {
                    console.warn(`✗ ${config.varName} not found or undefined`);
                }
            });
            
            // Area Kerja Layers
            const areaKerjaConfigs = [
                { varName: 'areaKerjaBmo1Fad', layerName: 'Area Kerja BMO1 FAD', zIndex: 411 },
                { varName: 'areaKerjaBmo1Kdc', layerName: 'Area Kerja BMO1 KDC', zIndex: 412 },
                { varName: 'areaKerjaBmo2Buma', layerName: 'Area Kerja BMO2 BUMA', zIndex: 413 },
                { varName: 'areaKerjaBmo3Bar', layerName: 'Area Kerja BMO3 BAR', zIndex: 414 },
                { varName: 'areaKerjaGmoKdc', layerName: 'Area Kerja GMO KDC', zIndex: 415 },
                { varName: 'areaKerjaGmoPama', layerName: 'Area Kerja GMO PAMA', zIndex: 415 },
                { varName: 'areaKerjaLmoBuma', layerName: 'Area Kerja LMO BUMA', zIndex: 416 },
                { varName: 'areaKerjaLmoFad', layerName: 'Area Kerja LMO FAD', zIndex: 417 },
                { varName: 'areaKerjaSmoMtn', layerName: 'Area Kerja SMO MTN', zIndex: 418 }
            ];
            
            areaKerjaConfigs.forEach(config => {
                if (typeof window[config.varName] !== 'undefined' && window[config.varName]) {
                    try {
                        console.log(`Loading ${config.layerName}...`);
                        console.log(`Data check - ${config.varName}:`, {
                            hasData: !!window[config.varName],
                            featuresCount: window[config.varName].features ? window[config.varName].features.length : 0,
                            crs: window[config.varName].crs
                        });
                        const layer = createLayerFromGeoJson32650(
                            window[config.varName],
                            config.layerName,
                            getAreaKerjaStyle,
                            config.zIndex
                        );
                        if (layer) {
                            const featureCount = layer.getSource().getFeatures().length;
                            console.log(`Layer created for ${config.layerName}, features:`, featureCount);
                            
                            // Always add layer to map, even if featureCount is 0
                            // Features might be loaded asynchronously or data might be empty
                            layer.setVisible(true);
                            layer.setOpacity(1.0);
                            layer.setZIndex(config.zIndex);
                            map.addLayer(layer);
                            areaKerjaLayers.push(layer);
                            
                            // Log layer details
                            const extent = layer.getSource().getExtent();
                            console.log(`✓ ${config.layerName} layer added successfully:`, {
                                features: featureCount,
                                visible: layer.getVisible(),
                                opacity: layer.getOpacity(),
                                zIndex: layer.getZIndex(),
                                extent: extent
                            });
                            
                            if (featureCount === 0) {
                                console.warn(`⚠ ${config.layerName} layer added but has no features - data might be loading or empty`);
                            }
                        } else {
                            console.error(`✗ Failed to create layer for ${config.layerName}`);
                        }
                    } catch (error) {
                        console.error(`Error creating ${config.layerName} layer:`, error);
                        console.error('Error stack:', error.stack);
                        console.error('Data sample:', JSON.stringify(window[config.varName]).substring(0, 500));
                    }
                } else {
                    console.warn(`✗ ${config.varName} not found or undefined`);
                }
            });
            
            // Add areaKerjaBmo2PamaLayer to areaKerjaLayers if it exists
            if (areaKerjaBmo2PamaLayer) {
                // Check if it's not already in the array
                const alreadyAdded = areaKerjaLayers.some(layer => layer === areaKerjaBmo2PamaLayer);
                if (!alreadyAdded) {
                    areaKerjaLayers.push(areaKerjaBmo2PamaLayer);
                    console.log('✓ Area Kerja BMO2 PAMA layer added to areaKerjaLayers array');
                }
                // Ensure it's visible
                areaKerjaBmo2PamaLayer.setVisible(true);
                areaKerjaBmo2PamaLayer.setOpacity(1.0);
            }
            
            // Ensure all area kerja layers are visible
            areaKerjaLayers.forEach(layer => {
                if (layer) {
                    layer.setVisible(true);
                    layer.setOpacity(1.0);
                    const layerName = layer.get('name') || 'Area Kerja';
                    const featureCount = layer.getSource().getFeatures().length;
                    console.log(`✓ ${layerName} layer set to visible (${featureCount} features)`);
                }
            });
            
            // Log summary of all loaded area kerja layers
            console.log(`\n=== AREA KERJA LAYERS SUMMARY ===`);
            console.log(`Total Area Kerja Layers: ${areaKerjaLayers.length}`);
            areaKerjaLayers.forEach((layer, index) => {
                const layerName = layer.get('name') || `Area Kerja ${index + 1}`;
                const featureCount = layer.getSource().getFeatures().length;
                const isVisible = layer.getVisible();
                const opacity = layer.getOpacity();
                console.log(`${index + 1}. ${layerName}: ${featureCount} features, visible: ${isVisible}, opacity: ${opacity}`);
            });
            console.log(`================================\n`);
            
            // Store layers globally for potential future use
            window.areaCctvLayers = areaCctvLayers;
            window.areaKerjaLayers = areaKerjaLayers;
            
            console.log(`Finished loading all layers - ${areaCctvLayers.length} Area CCTV layers, ${areaKerjaLayers.length} Area Kerja layers`);
            
            // Fit map to show all layers if there are any
            if (areaCctvLayers.length > 0 || areaKerjaLayers.length > 0) {
                const allLayers = [...areaCctvLayers, ...areaKerjaLayers];
                const extent = ol.extent.createEmpty();
                allLayers.forEach(layer => {
                    const source = layer.getSource();
                    if (source && source.getExtent) {
                        ol.extent.extend(extent, source.getExtent());
                    }
                });
                if (!ol.extent.isEmpty(extent)) {
                    map.getView().fit(extent, {
                        padding: [50, 50, 50, 50],
                        maxZoom: 18
                    });
                }
            }
        }
        
        // OPTIMIZED: Defer loading GeoJSON layers untuk performa
        // Load setelah marker selesai di-render
        // OPTIMIZED: Defer loading GeoJSON layers untuk performa
        // Load setelah marker selesai di-render dan user interaction
        console.log('Scheduling loadAllAreaLayers (deferred for performance)...');
        
        // Load GeoJSON layers setelah idle atau setelah 3 detik
        let loadTimeout = setTimeout(function() {
            console.log('Calling loadAllAreaLayers now (timeout)...');
            loadAllAreaLayers();
        }, 3000);
        
        // Cancel timeout jika user berinteraksi dengan map (zoom/pan)
        map.getView().on('change:resolution', function() {
            if (loadTimeout) {
                clearTimeout(loadTimeout);
                loadTimeout = null;
            }
        });
        
        // Load saat browser idle (jika supported)
        if ('requestIdleCallback' in window) {
            requestIdleCallback(function() {
                if (loadTimeout) {
                    clearTimeout(loadTimeout);
                    loadTimeout = null;
                }
                console.log('Calling loadAllAreaLayers now (idle)...');
                loadAllAreaLayers();
            }, { timeout: 3000 });
        } // Delay lebih lama untuk prioritize marker rendering
    }, 500);

    // Function to format area in square meters or hectares
    function formatArea(areaM2) {
        if (!areaM2 || areaM2 === 0) return '0 m²';
        if (areaM2 >= 10000) {
            const hectares = areaM2 / 10000;
            return `${hectares.toFixed(2)} Ha`;
        } else {
            return `${areaM2.toFixed(2)} m²`;
        }
    }

    // Function to populate coverage table
    let coverageTableData = [];
    let filteredCoverageTableData = [];
    let currentCoveragePage = 1;
    const coveragePerPage = 10;
    let totalCoverageStats = {
        totalAreaKerja: 0,
        totalCoveredArea: 0,
        coveragePercentage: 0
    };

    function populateCoverageTable() {
        const coverageTableBody = document.getElementById('coverageTableBody');
        if (!coverageTableBody) {
            console.warn('coverageTableBody element not found, skipping populateCoverageTable');
            return;
        }
        
        if (!intersectionBmo2PamaLayer || !areaKerjaBmo2PamaLayer) {
            coverageTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Data belum tersedia</td>
                </tr>
            `;
            return;
        }

        const intersections = intersectionBmo2PamaLayer.getSource().getFeatures();
        const areaKerjaFeatures = areaKerjaBmo2PamaLayer.getSource().getFeatures();
        
        // Calculate total area kerja using luasan from properties
        let totalAreaKerja = 0;
        areaKerjaFeatures.forEach(function(feature) {
            const luasan = feature.get('luasan');
            if (luasan && !isNaN(luasan)) {
                totalAreaKerja += parseFloat(luasan);
            }
        });
        
        // Calculate total covered area (from intersections) using luasan from properties
        let totalCoveredArea = 0;
        intersections.forEach(function(intersection) {
            const props = intersection.getProperties();
            const luasan = props.luasan || intersection.get('luasan');
            if (luasan && !isNaN(luasan)) {
                totalCoveredArea += parseFloat(luasan);
            }
        });
        
        // Calculate coverage percentage
        const coveragePercentage = totalAreaKerja > 0 ? (totalCoveredArea / totalAreaKerja) * 100 : 0;
        
        // Store total coverage stats
        totalCoverageStats = {
            totalAreaKerja: totalAreaKerja,
            totalCoveredArea: totalCoveredArea,
            coveragePercentage: coveragePercentage
        };
        
        // Group intersections by area kerja (id_lokasi)
        const coverageMap = new Map();
        
        intersections.forEach(function(intersection) {
            const props = intersection.getProperties();
            const idLokasi = props.id_lokasi;
            const lokasi = props.lokasi || 'Unknown';
            const nomorCctv = props.nomor_cctv || props.no_cctv || 'N/A';
            const namaCctv = props.nama_cctv || 'N/A';
            
            if (!idLokasi) return;
            
            // Get area from luasan property instead of calculating from geometry
            const luasan = props.luasan || intersection.get('luasan');
            const area = (luasan && !isNaN(luasan)) ? parseFloat(luasan) : 0;
            
            if (!coverageMap.has(idLokasi)) {
                // Find corresponding area kerja feature
                const areaKerjaFeature = areaKerjaFeatures.find(function(f) {
                    return f.get('id_lokasi') === idLokasi || f.get('lokasi') === lokasi;
                });
                
                // Get area kerja area from luasan property
                let areaKerjaArea = 0;
                if (areaKerjaFeature) {
                    const areaKerjaLuasan = areaKerjaFeature.get('luasan');
                    if (areaKerjaLuasan && !isNaN(areaKerjaLuasan)) {
                        areaKerjaArea = parseFloat(areaKerjaLuasan);
                    }
                }
                
                coverageMap.set(idLokasi, {
                    idLokasi: idLokasi,
                    lokasi: lokasi,
                    areaKerjaNama: areaKerjaFeature ? (areaKerjaFeature.get('lokasi') || areaKerjaFeature.get('nama') || lokasi) : lokasi,
                    areaKerjaArea: areaKerjaArea,
                    cctvList: [],
                    totalArea: 0
                });
            }
            
            const coverage = coverageMap.get(idLokasi);
            coverage.cctvList.push({
                nomor: nomorCctv,
                nama: namaCctv,
                area: area
            });
            coverage.totalArea += area;
        });
        
        // Convert to array and sort, calculate coverage percentage for each item
        coverageTableData = Array.from(coverageMap.values()).map(function(item, index) {
            const coveragePercentage = item.areaKerjaArea > 0 ? (item.totalArea / item.areaKerjaArea) * 100 : 0;
            return {
                ...item,
                index: index + 1,
                cctvCount: item.cctvList.length,
                cctvNames: item.cctvList.map(c => c.nomor).join(', '),
                coveragePercentage: coveragePercentage
            };
        });
        
        // Initialize filtered data
        filteredCoverageTableData = [...coverageTableData];
        
        // Show button if data available
        const btnShowDetail = document.getElementById('btnShowDetail');
        if (coverageTableData.length > 0 && btnShowDetail) {
            btnShowDetail.style.display = 'block';
        }
        
        renderCoverageTable();
    }

    function renderCoverageTable() {
        const tbody = document.getElementById('coverageTableBody');
        
        if (coverageTableData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Tidak ada data coverage</td>
                </tr>
            `;
            return;
        }
        
        // Filter data untuk area (B8) Rest area dan (B8) CPP saja
        const targetAreas = ['(B8) Rest area', '(B8) CPP'];
        const itemsToShow = coverageTableData.filter(function(item) {
            const areaName = item.areaKerjaNama || item.lokasi || '';
            return targetAreas.some(function(targetArea) {
                return areaName.includes(targetArea);
            });
        });
        
        if (itemsToShow.length > 0) {
            tbody.innerHTML = itemsToShow.map(function(item) {
                const statusBadge = item.cctvCount > 0 
                    ? '<span class="badge bg-success">Covered</span>' 
                    : '<span class="badge bg-warning">Partial</span>';
                
                return `
                    <tr>
                        <td colspan="5" class="py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <strong class="text-primary">${item.areaKerjaNama || item.lokasi}</strong>
                                        ${statusBadge}
                                    </div>
                                    <div class="text-muted small">
                                        <div><strong>CCTV Coverage:</strong> ${item.cctvNames || 'N/A'}</div>
                                        <div><strong>Luasan:</strong> ${formatArea(item.totalArea)}</div>
                                        <div><strong>Total CCTV:</strong> ${item.cctvCount} unit</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Data untuk area (B8) Rest area dan (B8) CPP tidak ditemukan</td>
                </tr>
            `;
        }
    }
    
    function renderCoverageDetailTable() {
        const tbody = document.getElementById('coverageDetailTableBody');
        const pagination = document.getElementById('coverageDetailPagination');
        const summaryStatsContainer = document.getElementById('coverageSummaryStats');
        
        // Render summary stats
        if (summaryStatsContainer && totalCoverageStats) {
            const stats = totalCoverageStats;
            const coveragePercentage = stats.coveragePercentage || 0;
            let percentageColor = 'success';
            if (coveragePercentage < 50) {
                percentageColor = 'danger';
            } else if (coveragePercentage < 80) {
                percentageColor = 'warning';
            }
            
            const bgColorClass = 'bg-' + percentageColor + ' bg-opacity-10';
            const textColorClass = 'text-' + percentageColor;
            const badgeClass = 'badge bg-' + percentageColor + ' fs-6';
            
            summaryStatsContainer.innerHTML = `
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                        <i class="material-icons-outlined text-primary" style="font-size: 32px;">location_on</i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Total Luasan Area Kerja</small>
                                        <h5 class="mb-0 fw-bold">${formatArea(stats.totalAreaKerja)}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                        <i class="material-icons-outlined text-success" style="font-size: 32px;">videocam</i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Total Tercover CCTV</small>
                                        <h5 class="mb-0 fw-bold">${formatArea(stats.totalCoveredArea)}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="${bgColorClass} rounded-circle p-3">
                                        <i class="material-icons-outlined ${textColorClass}" style="font-size: 32px;">percent</i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Persentase Coverage</small>
                                        <h5 class="mb-0 fw-bold">
                                            <span class="${badgeClass}">${coveragePercentage.toFixed(2)}%</span>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (filteredCoverageTableData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada data yang sesuai dengan pencarian</td>
                </tr>
            `;
            pagination.innerHTML = '';
            return;
        }
        
        const totalPages = Math.ceil(filteredCoverageTableData.length / coveragePerPage);
        const startIndex = (currentCoveragePage - 1) * coveragePerPage;
        const endIndex = startIndex + coveragePerPage;
        const pageData = filteredCoverageTableData.slice(startIndex, endIndex);
        
        tbody.innerHTML = pageData.map(function(item, idx) {
            const rowNum = startIndex + idx + 1;
            const statusBadge = item.cctvCount > 0 
                ? '<span class="badge bg-success">Covered</span>' 
                : '<span class="badge bg-warning">Partial</span>';
            
            // Calculate coverage percentage for this item
            const itemCoveragePercentage = item.coveragePercentage || 0;
            let percentageColor = 'success';
            if (itemCoveragePercentage < 50) {
                percentageColor = 'danger';
            } else if (itemCoveragePercentage < 80) {
                percentageColor = 'warning';
            }
            const percentageBadgeClass = 'badge bg-' + percentageColor;
            const percentageBadge = '<span class="' + percentageBadgeClass + '">' + itemCoveragePercentage.toFixed(2) + '%</span>';
            
            return `
                <tr>
                    <td>${rowNum}</td>
                    <td><strong>${item.areaKerjaNama || item.lokasi}</strong></td>
                    <td>
                        <small>${item.cctvNames || 'N/A'}</small>
                        ${item.cctvCount > 1 ? `<br><span class="badge bg-info bg-opacity-10 text-info">${item.cctvCount} CCTV</span>` : ''}
                    </td>
                    <td>${formatArea(item.totalArea)}</td>
                    <td>${percentageBadge}</td>
                    <td>${statusBadge}</td>
                </tr>
            `;
        }).join('');
        
        // Render pagination
        if (totalPages > 1) {
            let paginationHTML = '';
            
            // Previous button
            paginationHTML += `
                <li class="page-item ${currentCoveragePage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changeCoveragePage(${currentCoveragePage - 1})">Previous</a>
                </li>
            `;
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentCoveragePage - 1 && i <= currentCoveragePage + 1)) {
                    paginationHTML += `
                        <li class="page-item ${i === currentCoveragePage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0)" onclick="changeCoveragePage(${i})">${i}</a>
                        </li>
                    `;
                } else if (i === currentCoveragePage - 2 || i === currentCoveragePage + 2) {
                    paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }
            
            // Next button
            paginationHTML += `
                <li class="page-item ${currentCoveragePage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changeCoveragePage(${currentCoveragePage + 1})">Next</a>
                </li>
            `;
            
            pagination.innerHTML = paginationHTML;
        } else {
            pagination.innerHTML = '';
        }
    }

    function changeCoveragePage(page) {
        const totalPages = Math.ceil(filteredCoverageTableData.length / coveragePerPage);
        if (page < 1 || page > totalPages) return;
        currentCoveragePage = page;
        renderCoverageDetailTable();
    }
    
    function openCoverageModal() {
        // Reset to first page and clear search
        currentCoveragePage = 1;
        document.getElementById('coverageSearchInput').value = '';
        filteredCoverageTableData = [...coverageTableData];
        
        // Render table in modal
        renderCoverageDetailTable();
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('coverageDetailModal'));
        modal.show();
    }
    
    function filterCoverageTable() {
        const searchTerm = document.getElementById('coverageSearchInput').value.toLowerCase().trim();
        
        if (!searchTerm) {
            filteredCoverageTableData = [...coverageTableData];
        } else {
            filteredCoverageTableData = coverageTableData.filter(function(item) {
                const areaKerja = (item.areaKerjaNama || item.lokasi || '').toLowerCase();
                const cctvNames = (item.cctvNames || '').toLowerCase();
                return areaKerja.includes(searchTerm) || cctvNames.includes(searchTerm);
            });
        }
        
        currentCoveragePage = 1; // Reset to first page
        renderCoverageDetailTable();
    }
    
    function clearCoverageSearch() {
        document.getElementById('coverageSearchInput').value = '';
        filteredCoverageTableData = [...coverageTableData];
        currentCoveragePage = 1;
        renderCoverageDetailTable();
    }
    
    // Make functions globally accessible
    window.changeCoveragePage = changeCoveragePage;
    window.openCoverageModal = openCoverageModal;
    window.filterCoverageTable = filterCoverageTable;
    window.clearCoverageSearch = clearCoverageSearch;

    // Toggle GeoJSON layers visibility (only if elements exist)
    const showAreaKerjaBmo2Pama = document.getElementById('showAreaKerjaBmo2Pama');
    if (showAreaKerjaBmo2Pama) {
        showAreaKerjaBmo2Pama.addEventListener('change', function(e) {
            if (areaKerjaBmo2PamaLayer) {
                areaKerjaBmo2PamaLayer.setVisible(e.target.checked);
            }
        });
    }

    const showAreaCctvBmo2Pama = document.getElementById('showAreaCctvBmo2Pama');
    if (showAreaCctvBmo2Pama) {
        showAreaCctvBmo2Pama.addEventListener('change', function(e) {
            if (areaCctvBmo2PamaLayer) {
                areaCctvBmo2PamaLayer.setVisible(e.target.checked);
            }
        });
    }

    const showDifferenceBmo2Pama = document.getElementById('showDifferenceBmo2Pama');
    if (showDifferenceBmo2Pama) {
        showDifferenceBmo2Pama.addEventListener('change', function(e) {
            if (differenceBmo2PamaLayer) {
                differenceBmo2PamaLayer.setVisible(e.target.checked);
            }
        });
    }

    const showSymmetricalDifferenceBmo2Pama = document.getElementById('showSymmetricalDifferenceBmo2Pama');
    if (showSymmetricalDifferenceBmo2Pama) {
        showSymmetricalDifferenceBmo2Pama.addEventListener('change', function(e) {
            if (symmetricalDifferenceBmo2PamaLayer) {
                symmetricalDifferenceBmo2PamaLayer.setVisible(e.target.checked);
            }
        });
    }

    const showIntersectionBmo2Pama = document.getElementById('showIntersectionBmo2Pama');
    if (showIntersectionBmo2Pama) {
        showIntersectionBmo2Pama.addEventListener('change', function(e) {
            if (intersectionBmo2PamaLayer) {
                intersectionBmo2PamaLayer.setVisible(e.target.checked);
            }
        });
    }

    // Popup overlay
    const popupElement = document.getElementById('popup');
    // Site filter
    // siteFilter sudah diganti dengan mainFilterSiteBtn (dropdown button)
    // const siteFilter = document.getElementById('siteFilter');
    // currentSiteFilter sudah didefinisikan di atas
    popupOverlay = new ol.Overlay({
        element: popupElement,
        autoPan: {
            animation: {
                duration: 250
            }
        }
    });
    map.addOverlay(popupOverlay);

    // Popup closer
    const popupCloser = document.getElementById('popup-closer');
    popupCloser.onclick = function() {
        popupOverlay.setPosition(undefined);
        popupCloser.blur();
        return false;
    };

    // Click handler
    map.on('singleclick', async function(evt) {
        const feature = map.forEachFeatureAtPixel(evt.pixel, function(feature) {
            return feature;
        });

        if (feature) {
            const featureType = feature.get('type');
            if (featureType === 'insiden') {
                const data = feature.get('data');
                // Ensure area kerja layers remain visible when clicking insiden
                if (window.areaKerjaLayers && Array.isArray(window.areaKerjaLayers)) {
                    window.areaKerjaLayers.forEach(layer => {
                        if (layer) {
                            layer.setVisible(true);
                            layer.setOpacity(1.0);
                        }
                    });
                }
                if (areaKerjaBmo2PamaLayer) {
                    areaKerjaBmo2PamaLayer.setVisible(true);
                    areaKerjaBmo2PamaLayer.setOpacity(1.0);
                }
                // Force map render to ensure area kerja layers are visible
                if (map) {
                    map.render();
                }
                showInsidenPopup(evt.coordinate, data);
                return;
            }
            
            // Check if it's a unit vehicle marker
            if (featureType === 'unit_vehicle') {
                const unitData = feature.get('unitData');
                showUnitVehiclePopup(evt.coordinate, unitData);
                return;
            }
            
            // Check if it's a GPS Orang marker
            if (featureType === 'gps_orang') {
                const userData = feature.get('userData');
                showGpsOrangPopup(evt.coordinate, userData);
                return;
            }
            
            // Check if it's a CCTV marker
            if (featureType === 'cctv') {
                const cctv = feature.get('cctvData');
                if (cctv) {
                    showCCTVPopup(evt.coordinate, cctv);
                }
                return;
            }
            
            // Check if it's a hazard/SAP
            const data = feature.get('data');
            if (data) {
                // Clear area kerja highlight when clicking hazard/SAP
                if (highlightedAreaKerjaLayer) {
                    map.removeLayer(highlightedAreaKerjaLayer);
                    highlightedAreaKerjaLayer = null;
                }
                // Check if it's SAP data (has task_number or jenis_laporan)
                if (data.task_number || data.jenis_laporan) {
                    showSapPopup(evt.coordinate, data);
                } else {
                    showHazardPopup(evt.coordinate, data);
                }
                return;
            }
            
            // Check if it's a Daily Operation Plan polygon
            const props = feature.getProperties();
            if (props.id && props.pekerjaan && props.lokasi && props.detail_lokasi) {
                // Clear area kerja highlight when clicking daily operation plan
                if (highlightedAreaKerjaLayer) {
                    map.removeLayer(highlightedAreaKerjaLayer);
                    highlightedAreaKerjaLayer = null;
                }
                // This is a daily operation plan feature
                showDailyOperationPlanPopup(evt.coordinate, props);
                return;
            }
            
            // Check if it's a GeoJSON polygon (Area Kerja or Area CCTV)
            
            // Check for Area CCTV (has nomor_cctv property, even if null)
            const hasNomorCctv = 'nomor_cctv' in props;
            // Check for Area Kerja (has id_lokasi property OR has lokasi property with site/perusahaan)
            // Area Kerja BMO2 PAMA uses 'lokasi' instead of 'id_lokasi'
            const hasIdLokasi = 'id_lokasi' in props;
            const hasLokasi = 'lokasi' in props && ('site' in props || 'perusahaan' in props);
            const isAreaKerja = hasIdLokasi || (hasLokasi && !hasNomorCctv);
            
            if (hasNomorCctv || isAreaKerja) {
                let content = '';
                
                if (hasNomorCctv) {
                    // Area CCTV - Tambahkan tombol untuk filter SAP
                    const cctvNo = (props.nomor_cctv && props.nomor_cctv !== null && props.nomor_cctv !== 'null') ? props.nomor_cctv : 'N/A';
                    const cctvName = (props.nama_cctv && props.nama_cctv !== null && props.nama_cctv !== 'null') ? props.nama_cctv : 'N/A';
                    const cctvLokasi = props.coverage_lokasi || props.lokasi_pemasangan || props.coverage_detail_lokasi || props.lokasi || '';
                    const site = props.site || 'N/A';
                    const perusahaan = props.perusahaan_cctv || props.perusahaan || 'N/A';
                    const luasan = props.luasan ? props.luasan.toLocaleString('id-ID', {maximumFractionDigits: 2}) : 'N/A';
                    
                    content = `
                        <div style="min-width: 280px; background-color: #ffffff !important;">
                            <h6 style="margin: 0 0 10px 0;">Area CCTV</h6>
                            <p style="margin: 5px 0; font-size: 13px;"><strong>Nomor CCTV:</strong> ${cctvNo}</p>
                            <p style="margin: 5px 0; font-size: 13px;"><strong>Nama CCTV:</strong> ${cctvName}</p>
                            <p style="margin: 5px 0; font-size: 13px;"><strong>Lokasi:</strong> ${cctvLokasi || 'N/A'}</p>
                            <p style="margin: 5px 0; font-size: 13px;"><strong>Site:</strong> ${site}</p>
                            <p style="margin: 5px 0; font-size: 13px;"><strong>Perusahaan:</strong> ${perusahaan}</p>
                            <p style="margin: 5px 0; font-size: 13px;"><strong>Luasan:</strong> ${luasan} m²</p>
                            <hr style="margin: 10px 0;">
                            ${cctvNo !== 'N/A' ? `
                            <button class="btn btn-sm btn-primary w-100 mt-2" onclick="filterSapByAreaCctv('${cctvNo}', '${String(cctvName).replace(/'/g, "\\'")}', '${String(cctvLokasi).replace(/'/g, "\\'")}')">
                                <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">filter_list</i> Filter SAP di Area Ini
                            </button>
                            <button class="btn btn-sm btn-success w-100 mt-2" onclick="loadEvaluationSummary('area_cctv', null, null, '${cctvNo}', '${String(cctvName).replace(/'/g, "\\'")}')">
                                <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">assessment</i> Lihat Evaluasi
                            </button>
                            ` : ''}
                        </div>
                    `;
                    
                    document.getElementById('popup-content').innerHTML = content;
                    popupOverlay.setPosition(evt.coordinate);
                } else if (isAreaKerja) {
                    // Area Kerja - Show summary modal instead of popup
                    showAreaKerjaSummaryModal(feature, props);
                }
            }
        } else {
            // Clear highlight when clicking on empty area
            if (highlightedAreaKerjaLayer) {
                map.removeLayer(highlightedAreaKerjaLayer);
                highlightedAreaKerjaLayer = null;
            }
            popupOverlay.setPosition(undefined);
        }
    });

    function showDailyOperationPlanPopup(coordinate, plan) {
        const pekerjaan = plan.pekerjaan || 'N/A';
        const lokasi = plan.lokasi || 'N/A';
        const detailLokasi = plan.detail_lokasi || 'N/A';
        const unitId = plan.unit_id || 'N/A';
        const potensiResiko = plan.potensi_resiko || 'N/A';
        const pengendalianBahaya = plan.pengendalian_bahaya || 'N/A';
        const catatan = plan.catatan || 'N/A';
        const tanggal = plan.tanggal || 'N/A';
        const site = plan.site || 'N/A';
        const fotoPekerjaan = plan.foto_pekerjaan || null;
        
        let fotoHtml = '';
        if (fotoPekerjaan) {
            const fotoUrl = `{{ asset('storage/') }}/${fotoPekerjaan}`;
            fotoHtml = `
                <hr style="margin: 10px 0;">
                <p style="margin: 5px 0; font-size: 13px;"><strong>Foto Pekerjaan:</strong></p>
                <img src="${fotoUrl}" alt="Foto Pekerjaan" style="max-width: 100%; height: auto; border-radius: 4px; margin-top: 5px;" onerror="this.style.display='none'">
            `;
        }
        
        const content = `
            <div style="min-width: 300px; max-width: 400px; background-color: #ffffff !important;">
                <h6 style="margin: 0 0 10px 0; color: #1f2937;">Rencana Operasi Harian</h6>
                <p style="margin: 5px 0; font-size: 13px;"><strong>Pekerjaan:</strong> ${pekerjaan}</p>
                <p style="margin: 5px 0; font-size: 13px;"><strong>Lokasi:</strong> ${lokasi}</p>
                <p style="margin: 5px 0; font-size: 13px;"><strong>Detail Lokasi:</strong> ${detailLokasi}</p>
                <p style="margin: 5px 0; font-size: 13px;"><strong>Site:</strong> ${site}</p>
                <p style="margin: 5px 0; font-size: 13px;"><strong>Unit ID:</strong> ${unitId}</p>
                <p style="margin: 5px 0; font-size: 13px;"><strong>Tanggal:</strong> ${tanggal}</p>
                <hr style="margin: 10px 0;">
                <p style="margin: 5px 0; font-size: 13px;"><strong>Potensi Risiko:</strong></p>
                <p style="margin: 5px 0; font-size: 12px; color: #666; background-color: #ffffff !important;">${potensiResiko}</p>
                <hr style="margin: 10px 0;">
                <p style="margin: 5px 0; font-size: 13px;"><strong>Pengendalian Bahaya:</strong></p>
                <p style="margin: 5px 0; font-size: 12px; color: #666; background-color: #ffffff !important;">${pengendalianBahaya}</p>
                ${catatan !== 'N/A' ? `
                <hr style="margin: 10px 0;">
                <p style="margin: 5px 0; font-size: 13px;"><strong>Catatan:</strong></p>
                <p style="margin: 5px 0; font-size: 12px; color: #666; background-color: #ffffff !important;">${catatan}</p>
                ` : ''}
                ${fotoHtml}
            </div>
        `;
        document.getElementById('popup-content').innerHTML = content;
        popupOverlay.setPosition(coordinate);
    }
    
    function showHazardPopup(coordinate, hazard) {
        // Check if it's SAP data (has task_number or jenis_laporan)
        if (hazard.task_number || hazard.jenis_laporan) {
            showSapPopup(coordinate, hazard);
            return;
        }
        
        const content = `
            <div style="min-width: 200px; background-color: #ffffff !important;">
                <h6 style="margin: 0 0 10px 0;">${hazard.type}</h6>
                <p style="margin: 5px 0; font-size: 13px; background-color: #ffffff !important;">${hazard.description}</p>
                <p style="margin: 5px 0; font-size: 12px; color: #666; background-color: #ffffff !important;">
                    <strong>Severity:</strong> ${hazard.severity}<br>
                    <strong>Status:</strong> ${hazard.status}<br>
                    <strong>Lokasi:</strong> ${hazard.zone || 'Unknown'}<br>
                    <strong>Detected At:</strong> ${hazard.detected_at || 'N/A'}<br>
                    <strong>CCTV ID:</strong> ${hazard.cctv_id || 'N/A'}
                </p>
            </div>
        `;
        document.getElementById('popup-content').innerHTML = content;
        popupOverlay.setPosition(coordinate);
    }
    
    function showSapPopup(coordinate, sap) {
        // Langsung buka modal detail SAP tanpa menampilkan popup kecil
        // Close popup overlay jika ada
        if (popupOverlay) {
            popupOverlay.setPosition(undefined);
        }
        
        // Buka modal detail SAP langsung
        const taskNumber = sap.task_number || 'N/A';
        if (taskNumber && taskNumber !== 'N/A') {
            // Gunakan data SAP yang sudah ada untuk langsung buka modal
            const modal = new bootstrap.Modal(document.getElementById('sapDetailModal'));
            modal.show();
            
            // Populate modal dengan data SAP yang diklik
            populateSapDetailModal(sap);
        } else {
            // Fallback: coba cari data dari cache jika task_number tidak ada
            openSapDetailModalByData(sap);
        }
    }
    
    // Helper function untuk membuka modal SAP dengan data langsung
    function openSapDetailModalByData(sapData) {
        if (!sapData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Data SAP tidak tersedia',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('sapDetailModal'));
        modal.show();
        
        // Populate modal content
        populateSapDetailModal(sapData);
    }
    
    // Function to show Area Kerja Summary Modal with TARP
    async function showAreaKerjaSummaryModal(feature, props) {
        // Close popup first
        popupOverlay.setPosition(undefined);
        
        // Store area kerja data for intervensi button
        // Handle null values properly
        const getValue = (val) => {
            if (val === null || val === undefined || val === '' || val === 'null') {
                return null;
            }
            return val;
        };
        
        if (props) {
            window.currentAreaKerjaForIntervensi = {
                areaKerja: getValue(props.area_kerja) || getValue(props.areaKerja) || '',
                lokasi: getValue(props.lokasi) || ''
            };
        } else if (feature) {
            // Try to get from feature properties
            window.currentAreaKerjaForIntervensi = {
                areaKerja: getValue(feature.get('area_kerja')) || getValue(feature.get('areaKerja')) || '',
                lokasi: getValue(feature.get('lokasi')) || ''
            };
        }
        
        // Ensure lokasi is set - it's required
        if (!window.currentAreaKerjaForIntervensi || !window.currentAreaKerjaForIntervensi.lokasi) {
            console.warn('Lokasi tidak ditemukan untuk intervensi');
        }
        
        // Show loading in modal
        const modalBody = document.getElementById('areaKerjaSummaryModalBody');
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat data area kerja...</p>
            </div>
        `;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('areaKerjaSummaryModal'));
        modal.show();
        
        const lokasiNameFinal = getValue(props.lokasi);
        const areaKerjaId = getValue(props.id_lokasi) || getValue(props.fid) || getValue(props.lokasi);
        const site = getValue(props.site);
        const perusahaan = getValue(props.perusahaan);
        const areaKerja = getValue(props.area_kerja);
        const luasan = props.luasan && props.luasan !== null && !isNaN(props.luasan)
            ? parseFloat(props.luasan).toLocaleString('id-ID', {maximumFractionDigits: 2})
            : 'N/A';
        
        try {
            // Get risk matrix summary
            const riskSummary = await getRiskMatrixSummary(feature);
            
            // Generate AI-based recommendations
            let aiRecommendations = [];
            let aiLoading = true;
            
            try {
                // Prepare data for AI API
                const aiRequestData = {
                    risk_summary: {
                        risk_level: riskSummary.riskLevel,
                        has_sap_report: riskSummary.hasSapReport,
                        has_online_cctv: riskSummary.hasOnlineCctv,
                        is_high_risk_area: riskSummary.isHighRiskArea,
                        has_sap_in_high_risk_area: riskSummary.hasSapInHighRiskArea
                    },
                    cctv_list: riskSummary.cctvList.map(cctv => ({
                        nama_cctv: cctv.nama_cctv || cctv.name || cctv.no_cctv || cctv.nomor_cctv,
                        no_cctv: cctv.no_cctv || cctv.nomor_cctv,
                        nomor_cctv: cctv.no_cctv || cctv.nomor_cctv,
                        kondisi: cctv.kondisi || cctv.status,
                        status: cctv.status,
                        connected: cctv.connected,
                        is_online: cctv.is_online,
                        status_online: cctv.status_online,
                        lokasi_pemasangan: cctv.lokasi_pemasangan || cctv.coverage_detail_lokasi || cctv.coverage_lokasi,
                        coverage_lokasi: cctv.coverage_lokasi,
                        coverage_detail_lokasi: cctv.coverage_detail_lokasi || cctv.lokasi_pemasangan || cctv.coverage_lokasi
                    })),
                    sap_reports: riskSummary.sapReports.map(sap => {
                        // Get waktu from formatted waktu field, or from jam:menit, or from tanggal
                        let waktu = sap.waktu || '';
                        if (!waktu) {
                            const jam = sap.jam || null;
                            const menit = sap.menit || null;
                            if (jam !== null && menit !== null) {
                                const jamInt = parseInt(jam);
                                const menitInt = parseInt(menit);
                                if (jamInt >= 0 && jamInt <= 23 && menitInt >= 0 && menitInt <= 59) {
                                    waktu = `${String(jamInt).padStart(2, '0')}:${String(menitInt).padStart(2, '0')}`;
                                }
                            }
                        }
                        if (!waktu) {
                            const tanggal = sap.tanggal_pelaporan || sap.detected_at;
                            if (tanggal) {
                                try {
                                    const date = new Date(tanggal);
                                    waktu = `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
                                } catch (e) {}
                            }
                        }
                        
                        // Prefer nama_lokasi over lokasi
                        const namaLokasi = sap.nama_lokasi || sap.lokasi || null;
                        const namaDetailLokasi = sap.nama_detail_lokasi || sap.detail_lokasi || null;
                        const lokasi = namaLokasi || namaDetailLokasi || 'N/A';
                        
                        // Get deskripsi/keterangan
                        const deskripsi = sap.keterangan || sap.deskripsi || sap.aktivitas_pekerjaan || sap.description || null;
                        
                        return {
                            task_number: sap.task_number || sap.id,
                            jenis_laporan: sap.jenis_laporan || sap.source_type || sap.type,
                            lokasi: lokasi,
                            nama_lokasi: namaLokasi,
                            nama_detail_lokasi: namaDetailLokasi,
                            deskripsi: deskripsi,
                            waktu: waktu,
                            jam: sap.jam,
                            menit: sap.menit,
                            tanggal_pelaporan: sap.tanggal_pelaporan || sap.detected_at
                        };
                    }),
                    area_info: {
                        lokasi: lokasiNameFinal,
                        nama_lokasi: lokasiNameFinal,
                        id_lokasi: areaKerjaId,
                        site: site,
                        perusahaan: perusahaan,
                        area_kerja: areaKerja,
                        luasan: luasan
                    }
                };
                
                // Call AI API
                const aiResponse = await fetch('{{ route("full-maps.api.generate-recommendations") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(aiRequestData)
                });
                
                const aiResult = await aiResponse.json();
                
                if (aiResult.success && aiResult.recommendations && aiResult.recommendations.length > 0) {
                    aiRecommendations = aiResult.recommendations;
                } else {
                    // Fallback to static recommendations if AI fails
                    aiRecommendations = getFallbackRecommendations(riskSummary.riskLevel, riskSummary);
                }
            } catch (aiError) {
                console.error('Error generating AI recommendations:', aiError);
                // Fallback to static recommendations
                aiRecommendations = getFallbackRecommendations(riskSummary.riskLevel, riskSummary);
            }
            
            aiLoading = false;
            
            // Fallback function for recommendations - using Context → Insight → Action format
            function getFallbackRecommendations(riskLevel, riskSummary) {
                const recommendations = [];
                const cctvList = riskSummary.cctvList || [];
                const sapReports = riskSummary.sapReports || [];
                const cctvCount = cctvList.length;
                
                // Get sample CCTV numbers
                const onlineCctv = cctvList.filter(cctv => {
                    const kondisi = (cctv.kondisi || cctv.status || '').toLowerCase();
                    return kondisi === 'baik' || kondisi === 'online' || 
                           (cctv.status || '').toLowerCase() === 'live view';
                }).slice(0, 3);
                
                const offlineCctv = cctvList.filter(cctv => {
                    const kondisi = (cctv.kondisi || cctv.status || '').toLowerCase();
                    return kondisi !== 'baik' && kondisi !== 'online' && 
                           (cctv.status || '').toLowerCase() !== 'live view';
                }).slice(0, 2);
                
                // Get sample SAP
                const sampleSap = sapReports.length > 0 ? sapReports[0] : null;
                
                if (riskLevel === 'HIGH') {
                    if (sampleSap) {
                        const taskNumber = sampleSap.task_number || sampleSap.id || 'N/A';
                        const jenis = sampleSap.jenis_laporan || sampleSap.source_type || 'SAP';
                        
                        // Prefer nama_lokasi over lokasi
                        const namaLokasi = sampleSap.nama_lokasi || sampleSap.lokasi || null;
                        const namaDetailLokasi = sampleSap.nama_detail_lokasi || sampleSap.detail_lokasi || null;
                        const lokasi = namaLokasi || namaDetailLokasi || 'area ini';
                        
                        // Get waktu from formatted waktu, or from jam:menit, or from tanggal
                        let waktuStr = '';
                        if (sampleSap.waktu) {
                            waktuStr = ` pukul ${sampleSap.waktu}`;
                        } else {
                            const jam = sampleSap.jam || null;
                            const menit = sampleSap.menit || null;
                            if (jam !== null && menit !== null) {
                                const jamInt = parseInt(jam);
                                const menitInt = parseInt(menit);
                                if (jamInt >= 0 && jamInt <= 23 && menitInt >= 0 && menitInt <= 59) {
                                    waktuStr = ` pukul ${String(jamInt).padStart(2, '0')}:${String(menitInt).padStart(2, '0')}`;
                                }
                            }
                        }
                        
                        if (!waktuStr && (sampleSap.tanggal_pelaporan || sampleSap.detected_at)) {
                            try {
                                const date = new Date(sampleSap.tanggal_pelaporan || sampleSap.detected_at);
                                waktuStr = ` pukul ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                            } catch (e) {}
                        }
                        
                        // Get deskripsi/keterangan
                        const deskripsi = sampleSap.keterangan || sampleSap.deskripsi || sampleSap.aktivitas_pekerjaan || sampleSap.description || null;
                        const deskripsiStr = deskripsi ? ` terkait '${deskripsi}'` : '';
                        
                        recommendations.push({
                            priority: 'HIGH',
                            action: `Terdapat Temuan ${jenis} #${taskNumber}${deskripsiStr} yang dilaporkan${waktuStr} di ${lokasi}, pastikan temuan (jika ada) telah ditindaklanjuti oleh tim lapangan dan tidak ada kondisi yang memerlukan perhatian khusus.`
                        });
                    }
                    
                    if (offlineCctv.length > 0) {
                        const cctvNos = offlineCctv.map(c => c.no_cctv || c.nomor_cctv || c.nama_cctv).join(', ');
                        recommendations.push({
                            priority: 'HIGH',
                            action: `Segera koordinasi dengan IT Mitra untuk memperbaiki CCTV yang offline (${cctvNos}) di area ${lokasiNameFinal || 'ini'}, mengingat area ini memiliki risk level tinggi dan memerlukan monitoring optimal untuk mencegah potensi insiden.`
                        });
                    }
                    
                    recommendations.push({
                        priority: 'HIGH',
                        action: `Koordinasi dengan Safety dan Mining Superintendet BC untuk memberikan teguran terhadap PJA dan IT Mitra jika tidak ada follow up utilisasi CCTV dan perbaikan status offline CCTV 3 hari berturut-turut di area ${lokasiNameFinal || 'ini'}, mengingat area ini memiliki risk level tinggi dan memerlukan monitoring optimal.`
                    });
                } else if (riskLevel === 'MEDIUM') {
                    if (onlineCctv.length > 0 && riskSummary.isHighRiskArea) {
                        const cctvNos = onlineCctv.slice(0, 2).map(c => c.no_cctv || c.nomor_cctv || c.nama_cctv).join(' dan ');
                        const lokasiList = onlineCctv.slice(0, 2).map(c => c.lokasi_pemasangan || c.coverage_detail_lokasi || c.coverage_lokasi || lokasiNameFinal).filter((v, i, a) => a.indexOf(v) === i);
                        const lokasiStr = lokasiList.join(' dan ');
                        
                        recommendations.push({
                            priority: 'MEDIUM',
                            action: `Fokuskan pemantauan real-time pada aktivitas di ${lokasiStr}, karena kedua lokasi tersebut memiliki CCTV aktif (${cctvNos}) dan termasuk dalam zona kritis meskipun tidak diklasifikasikan sebagai high-risk hari ini.`
                        });
                    }
                    
                    if (sampleSap) {
                        const taskNumber = sampleSap.task_number || sampleSap.id || 'N/A';
                        const jenis = sampleSap.jenis_laporan || sampleSap.source_type || 'SAP';
                        
                        // Prefer nama_lokasi over lokasi
                        const namaLokasi = sampleSap.nama_lokasi || sampleSap.lokasi || null;
                        const namaDetailLokasi = sampleSap.nama_detail_lokasi || sampleSap.detail_lokasi || null;
                        const lokasi = namaLokasi || namaDetailLokasi || 'area ini';
                        
                        // Get waktu from formatted waktu, or from jam:menit, or from tanggal
                        let waktuStr = '';
                        if (sampleSap.waktu) {
                            waktuStr = ` pukul ${sampleSap.waktu}`;
                        } else {
                            const jam = sampleSap.jam || null;
                            const menit = sampleSap.menit || null;
                            if (jam !== null && menit !== null) {
                                const jamInt = parseInt(jam);
                                const menitInt = parseInt(menit);
                                if (jamInt >= 0 && jamInt <= 23 && menitInt >= 0 && menitInt <= 59) {
                                    waktuStr = ` pukul ${String(jamInt).padStart(2, '0')}:${String(menitInt).padStart(2, '0')}`;
                                }
                            }
                        }
                        
                        if (!waktuStr && (sampleSap.tanggal_pelaporan || sampleSap.detected_at)) {
                            try {
                                const date = new Date(sampleSap.tanggal_pelaporan || sampleSap.detected_at);
                                waktuStr = ` pukul ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                            } catch (e) {}
                        }
                        
                        // Get deskripsi/keterangan
                        const deskripsi = sampleSap.keterangan || sampleSap.deskripsi || sampleSap.aktivitas_pekerjaan || sampleSap.description || null;
                        const deskripsiStr = deskripsi ? ` terkait '${deskripsi}'` : '';
                        
                        recommendations.push({
                            priority: 'MEDIUM',
                            action: `Terdapat Temuan ${jenis} #${taskNumber}${deskripsiStr} yang dilaporkan${waktuStr} di ${lokasi}, pastikan temuan (jika ada) telah ditindaklanjuti oleh tim lapangan dan tidak ada kondisi yang memerlukan perhatian khusus.`
                        });
                    }
                    
                    recommendations.push({
                        priority: 'MEDIUM',
                        action: `Pengawas Control Room wajib melakukan pemeriksaan kondisi aktivitas highrisk minimal 3 kali dalam shift ini di area ${lokasiNameFinal || 'ini'}, dengan fokus pada area yang memiliki potensi risiko sedang untuk mencegah eskalasi kondisi.`
                    });
                    
                    if (offlineCctv.length > 0) {
                        const cctvNos = offlineCctv.map(c => c.no_cctv || c.nomor_cctv || c.nama_cctv).join(', ');
                        recommendations.push({
                            priority: 'MEDIUM',
                            action: `Koordinasi dengan IT Mitra Kerja dan Berau Coal untuk memfollow up kondisi status offline CCTV ${cctvNos} dan memastikan kondisi jaringan internet lancar dan tersedia di area ${lokasiNameFinal || 'ini'}, mengingat pentingnya monitoring kontinyu untuk area dengan risk level sedang.`
                        });
                    }
                } else {
                    // NORMAL/LOW risk - Format Context → Insight → Action
                    if (onlineCctv.length > 0 && cctvCount > 0) {
                        const cctvNos = onlineCctv.slice(0, 3).map(c => c.no_cctv || c.nomor_cctv || c.nama_cctv).join(', ');
                        const lokasiList = onlineCctv.slice(0, 2).map(c => c.lokasi_pemasangan || c.coverage_detail_lokasi || c.coverage_lokasi || lokasiNameFinal).filter((v, i, a) => a.indexOf(v) === i);
                        const lokasiStr = lokasiList.length > 0 ? lokasiList.join(' dan ') : (lokasiNameFinal || 'area ini');
                        
                        recommendations.push({
                            priority: 'LOW',
                            action: `Fokuskan pemantauan real-time pada aktivitas di ${lokasiStr}, karena lokasi tersebut memiliki CCTV aktif (${cctvNos}) dan memerlukan monitoring rutin untuk memastikan operasi berjalan sesuai standar keselamatan.`
                        });
                    }
                    
                    if (cctvCount > 0) {
                        recommendations.push({
                            priority: 'LOW',
                            action: `Dokumentasikan penggunaan seluruh ${cctvCount} CCTV dalam shift ini sebagai bukti utilitas sistem, khususnya untuk kamera yang memantau aktivitas operasional rutin di area ${lokasiNameFinal || 'ini'}.`
                        });
                    }
                    
                    if (onlineCctv.length > 0) {
                        const cctvNos = onlineCctv.slice(0, 2).map(c => c.no_cctv || c.nomor_cctv || c.nama_cctv).join(' dan ');
                        const lokasiList = onlineCctv.slice(0, 2).map(c => c.lokasi_pemasangan || c.coverage_detail_lokasi || c.coverage_lokasi || lokasiNameFinal).filter((v, i, a) => a.indexOf(v) === i);
                        const lokasiStr = lokasiList.length > 0 ? lokasiList.join(' dan ') : (lokasiNameFinal || 'area ini');
                        
                        recommendations.push({
                            priority: 'LOW',
                            action: `Gunakan kamera ${cctvNos} untuk melakukan patroli visual rutin terhadap aktivitas di ${lokasiStr}, mengingat pentingnya memastikan prosedur keselamatan diterapkan dengan baik di setiap tahap operasi.`
                        });
                    }
                    
                    if (riskSummary.hasSapReport && sampleSap) {
                        const taskNumber = sampleSap.task_number || sampleSap.id || 'N/A';
                        const jenis = sampleSap.jenis_laporan || sampleSap.source_type || 'SAP';
                        
                        // Prefer nama_lokasi over lokasi
                        const namaLokasi = sampleSap.nama_lokasi || sampleSap.lokasi || null;
                        const namaDetailLokasi = sampleSap.nama_detail_lokasi || sampleSap.detail_lokasi || null;
                        const lokasi = namaLokasi || namaDetailLokasi || 'area ini';
                        
                        // Get waktu from formatted waktu, or from jam:menit, or from tanggal
                        let waktuStr = '';
                        if (sampleSap.waktu) {
                            waktuStr = ` pukul ${sampleSap.waktu}`;
                        } else {
                            const jam = sampleSap.jam || null;
                            const menit = sampleSap.menit || null;
                            if (jam !== null && menit !== null) {
                                const jamInt = parseInt(jam);
                                const menitInt = parseInt(menit);
                                if (jamInt >= 0 && jamInt <= 23 && menitInt >= 0 && menitInt <= 59) {
                                    waktuStr = ` pukul ${String(jamInt).padStart(2, '0')}:${String(menitInt).padStart(2, '0')}`;
                                }
                            }
                        }
                        
                        if (!waktuStr && (sampleSap.tanggal_pelaporan || sampleSap.detected_at)) {
                            try {
                                const date = new Date(sampleSap.tanggal_pelaporan || sampleSap.detected_at);
                                waktuStr = ` pukul ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                            } catch (e) {}
                        }
                        
                        // Get deskripsi/keterangan
                        const deskripsi = sampleSap.keterangan || sampleSap.deskripsi || sampleSap.aktivitas_pekerjaan || sampleSap.description || null;
                        const deskripsiStr = deskripsi ? ` terkait '${deskripsi}'` : '';
                        
                        recommendations.push({
                            priority: 'LOW',
                            action: `Terdapat temuan ${jenis} #${taskNumber}${deskripsiStr} yang dilaporkan${waktuStr} di ${lokasi}, pastikan temuan (jika ada) telah ditindaklanjuti oleh tim lapangan dan tidak ada kondisi yang memerlukan perhatian khusus.`
                        });
                    } else if (cctvCount > 0) {
                        const cctvNos = onlineCctv.length > 0 ? onlineCctv.slice(0, 3).map(c => c.no_cctv || c.nomor_cctv || c.nama_cctv).join(', ') : '';
                        if (cctvNos) {
                            recommendations.push({
                                priority: 'LOW',
                                action: `Lakukan verifikasi status dan kualitas sinyal pada kamera ${cctvNos} di awal shift, karena kamera tersebut memantau aktivitas di area ${lokasiNameFinal || 'ini'} dan memerlukan kondisi optimal untuk memastikan monitoring berjalan efektif sepanjang shift.`
                            });
                        } else {
                            recommendations.push({
                                priority: 'LOW',
                                action: `Lakukan verifikasi status seluruh ${cctvCount} CCTV di area ${lokasiNameFinal || 'ini'} pada awal shift, pastikan semua kamera dalam kondisi baik dan dapat diakses untuk monitoring aktivitas operasional, mengingat pentingnya visibilitas kontinyu untuk menjaga standar keselamatan.`
                            });
                        }
                    }
                }
                
                return recommendations;
            }
            
            // Format CCTV list
            const cctvListHtml = riskSummary.cctvList.length > 0 ? riskSummary.cctvList.map((cctv, index) => {
                const cctvName = cctv.nama_cctv || cctv.name || cctv.no_cctv || cctv.nomor_cctv || 'CCTV ' + (index + 1);
                const cctvNo = cctv.no_cctv || cctv.nomor_cctv || 'N/A';
                const kondisi = cctv.kondisi || cctv.status || 'Unknown';
                const kondisiLower = (kondisi || '').toLowerCase();
                const isOnline = kondisiLower === 'baik' || kondisiLower === 'online' || 
                               (cctv.status || '').toLowerCase() === 'live view' || 
                               (cctv.connected || '').toLowerCase() === 'yes' ||
                               cctv.status === 1 || cctv.is_online === true || cctv.status_online === 1;
                const statusColor = isOnline ? '#22c55e' : '#dc2626';
                const statusText = isOnline ? 'Online' : 'Offline';
                
                return `
                    <div class="card mb-2">
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="font-size: 14px;">${cctvName}</h6>
                                    <small class="text-muted">No: ${cctvNo} | <span style="color: ${statusColor};">${statusText}</span></small>
                                    ${cctv.lokasi_pemasangan || cctv.coverage_lokasi || cctv.coverage_detail_lokasi ? `
                                    <br><small class="text-muted">${cctv.coverage_detail_lokasi || cctv.coverage_lokasi || cctv.lokasi_pemasangan}</small>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('') : '<p class="text-muted">Tidak ada CCTV ditemukan di area ini</p>';
            
            // Format SAP reports list
            const sapReportsHtml = riskSummary.sapReports.length > 0 ? riskSummary.sapReports.map((sap, index) => {
                const taskNumber = sap.task_number || sap.id || 'N/A';
                const jenisLaporan = sap.jenis_laporan || sap.source_type || sap.type || 'SAP';
                const lokasi = sap.lokasi || sap.detail_lokasi || 'N/A';
                const tanggal = sap.tanggal_pelaporan || sap.detected_at || 'N/A';
                let tanggalFormatted = 'N/A';
                if (tanggal !== 'N/A') {
                    try {
                        const date = new Date(tanggal);
                        tanggalFormatted = date.toLocaleDateString('id-ID', { 
                            day: '2-digit', 
                            month: '2-digit', 
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } catch (e) {
                        tanggalFormatted = tanggal;
                    }
                }
                
                return `
                    <div class="card mb-2">
                        <div class="card-body p-2">
                            <h6 class="mb-1" style="font-size: 14px;">${jenisLaporan}</h6>
                            <small class="text-muted">Task: ${taskNumber}</small>
                            <br><small class="text-muted">Lokasi: ${lokasi}</small>
                            <br><small class="text-muted">Tanggal: ${tanggalFormatted}</small>
                        </div>
                    </div>
                `;
            }).join('') : '<p class="text-muted">Tidak ada laporan SAP hari ini di area ini</p>';
            
            // Build modal content
            const modalContent = `
                <div class="row">
                    <!-- Left Column: Area Info & Risk Summary -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="material-icons-outlined">info</i> Informasi Area Kerja</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Lokasi:</strong> ${lokasiNameFinal || 'N/A'}</p>
                                ${props.id_lokasi ? `<p class="mb-2"><strong>ID Lokasi:</strong> ${props.id_lokasi}</p>` : ''}
                                ${props.fid && !props.id_lokasi ? `<p class="mb-2"><strong>ID:</strong> ${props.fid}</p>` : ''}
                                <p class="mb-2"><strong>Site:</strong> ${site || 'N/A'}</p>
                                <p class="mb-2"><strong>Perusahaan:</strong> ${perusahaan || 'N/A'}</p>
                                <p class="mb-2"><strong>Area Kerja:</strong> ${areaKerja || 'N/A'}</p>
                                <p class="mb-0"><strong>Luasan:</strong> ${luasan} m²</p>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header" style="background-color: ${riskSummary.riskColor};">
                                <h6 class="mb-0 text-white"><i class="material-icons-outlined">assessment</i> Risk Matrix Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h5 class="mb-0" style="color: ${riskSummary.riskColor}; font-weight: bold;">Risk Level: ${riskSummary.riskLevel}</h5>
                                </div>
                                
                                <div class="mb-2">
                                    <span style="display: inline-block; width: 25px; text-align: center; font-size: 18px;">${riskSummary.hasSapReport ? '✓' : '✗'}</span>
                                    <strong>Terdapat Laporan SAP:</strong> 
                                    <span style="color: ${riskSummary.hasSapReport ? '#22c55e' : '#dc2626'};">
                                        ${riskSummary.hasSapReport ? 'MEMENUHI' : 'TIDAK MEMENUHI'}
                                    </span>
                                </div>
                                
                                <div class="mb-2">
                                    <span style="display: inline-block; width: 25px; text-align: center; font-size: 18px;">${riskSummary.hasOnlineCctv ? '✓' : '✗'}</span>
                                    <strong>CCTV Kondisi Online:</strong> 
                                    <span style="color: ${riskSummary.hasOnlineCctv ? '#22c55e' : '#dc2626'};">
                                        ${riskSummary.hasOnlineCctv ? 'MEMENUHI' : 'TIDAK MEMENUHI'}
                                    </span>
                                    ${riskSummary.cctvCount > 0 ? ` <small class="text-muted">(${riskSummary.cctvCount} CCTV ditemukan)</small>` : ''}
                                </div>
                                
                                <div class="mb-2">
                                    <span style="display: inline-block; width: 25px; text-align: center; font-size: 18px;">${riskSummary.isHighRiskArea ? '⚠' : '○'}</span>
                                    <strong>Area Highrisk:</strong> 
                                    <span style="color: ${riskSummary.isHighRiskArea ? '#f59e0b' : '#6b7280'};">
                                        ${riskSummary.isHighRiskArea ? 'YA' : 'TIDAK'}
                                    </span>
                                </div>
                                
                                ${riskSummary.isHighRiskArea ? `
                                <div class="mb-2">
                                    <span style="display: inline-block; width: 25px; text-align: center; font-size: 18px;">${riskSummary.hasSapInHighRiskArea ? '✓' : '✗'}</span>
                                    <strong>Area Highrisk ada Laporan SAP:</strong> 
                                    <span style="color: ${riskSummary.hasSapInHighRiskArea ? '#22c55e' : '#dc2626'};">
                                        ${riskSummary.hasSapInHighRiskArea ? 'MEMENUHI' : 'TIDAK MEMENUHI'}
                                    </span>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: TARP Actions -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header ">
                                <h6 class="mb-0"><i class="material-icons-outlined">rule</i> TARP (Triggered Action Response Plan)</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Level:</strong> <span style="color: ${riskSummary.riskColor}; font-weight: bold;">${riskSummary.riskLevel}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Warna:</strong> 
                                    ${riskSummary.riskLevel === 'HIGH' ? 'Merah' : riskSummary.riskLevel === 'MEDIUM' ? 'Orange' : 'Hijau'}
                                </div>
                                <div class="mb-3">
                                    <strong>Kriteria:</strong> 
                                    ${riskSummary.riskLevel === 'HIGH' ? 'Risiko tinggi, pelanggaran kritikal, potensi fatal' : 
                                      riskSummary.riskLevel === 'MEDIUM' ? 'Potensi moderate, closed loop tidak tuntas' : 
                                      'Operasi sesuai standar'}
                                </div>
                                
                                <hr>
                                
                                <div class="mb-3">
                                    <h6 class="mb-1"><strong>Rekomendasi Tindakan untuk Pengawas Control Room</strong></h6>
                                    <small class="text-muted">${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</small>
                                </div>
                                
                                ${aiLoading ? `
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted small">Membangkitkan rekomendasi AI...</p>
                                </div>
                                ` : aiRecommendations.length > 0 ? `
                                <div class="mb-0">
                                    ${aiRecommendations.map((rec, index) => {
                                        const priorityColor = rec.priority === 'HIGH' ? '#dc2626' : 
                                                             rec.priority === 'MEDIUM' ? '#f59e0b' : '#22c55e';
                                        const priorityText = rec.priority === 'HIGH' ? 'Tinggi' : 
                                                            rec.priority === 'MEDIUM' ? 'Sedang' : 'Rendah';
                                        return `
                                        <div class="mb-3 p-3 border rounded" style="border-left: 4px solid ${priorityColor} !important; background-color: #f8f9fa;">
                                            <div class="d-flex align-items-start">
                                                <span class="badge me-2 mt-1" style="background-color: ${priorityColor}; min-width: 60px; font-size: 11px;">${priorityText}</span>
                                                <div class="flex-grow-1" style="line-height: 1.7;">
                                                    <p class="mb-0" style="line-height: 1.7; font-size: 14px;">${rec.action}</p>
                                                </div>
                                            </div>
                                        </div>
                                        `;
                                    }).join('')}
                                </div>
                                ` : '<p class="text-muted">Tidak ada tindakan khusus yang diperlukan.</p>'}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <!-- CCTV List -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="material-icons-outlined">videocam</i> CCTV di Area (${riskSummary.cctvList.length})</h6>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                ${cctvListHtml}
                            </div>
                        </div>
                    </div>
                    
                    <!-- SAP Reports List -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="material-icons-outlined">description</i> Laporan SAP Hari Ini (${riskSummary.sapReports.length})</h6>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                ${sapReportsHtml}
                            </div>
                        </div>
                    </div>
                </div>
                
                ${areaKerjaId ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <button class="btn btn-primary me-2" onclick="filterSapByAreaKerja('${areaKerjaId}', '${String(lokasiNameFinal || '').replace(/'/g, "\\'")}'); bootstrap.Modal.getInstance(document.getElementById('areaKerjaSummaryModal')).hide();">
                            <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">filter_list</i> Filter SAP di Area Ini
                        </button>
                        <button class="btn btn-success" onclick="loadEvaluationSummary('area_kerja', '${areaKerjaId}', '${String(lokasiNameFinal || '').replace(/'/g, "\\'")}', null, null); bootstrap.Modal.getInstance(document.getElementById('areaKerjaSummaryModal')).hide();">
                            <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">assessment</i> Lihat Evaluasi
                        </button>
                    </div>
                </div>
                ` : ''}
            `;
            
            modalBody.innerHTML = modalContent;
        } catch (error) {
            console.error('Error loading area kerja summary:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="material-icons-outlined">error_outline</i> Error memuat data area kerja: ${error.message}
                </div>
            `;
        }
    }
    
    // Function to open SAP detail modal (make it globally accessible)
    window.openSapDetailModal = function(taskNumber) {
        if (!taskNumber || taskNumber === 'N/A') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Task number tidak tersedia',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Find SAP data by task_number from both global sapData and filteredSidebarData
        let foundSapData = null;
        const globalSapData = typeof window.sapData !== 'undefined' ? window.sapData : sapData;
        if (globalSapData && Array.isArray(globalSapData)) {
            foundSapData = globalSapData.find(s => s.task_number === taskNumber);
        }
        if (!foundSapData && typeof filteredSidebarData !== 'undefined' && filteredSidebarData.sap) {
            foundSapData = filteredSidebarData.sap.find(s => s.task_number === taskNumber);
        }
        
        if (!foundSapData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Data SAP tidak ditemukan',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('sapDetailModal'));
        modal.show();
        
        // Populate modal content
        populateSapDetailModal(foundSapData);
    };
    
    // Function to populate SAP detail modal
    function populateSapDetailModal(sap) {
        const modalBody = document.getElementById('sapDetailModalBody');
        const modalTitle = document.getElementById('sapDetailModalLabel');
        
        // Set title
        modalTitle.textContent = `Detail SAP - ${sap.jenis_laporan || 'SAP'} ${sap.task_number || ''}`;
        
        // Format tanggal
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            try {
                const date = new Date(dateStr);
                // Kurangi 7 jam dari waktu database
                date.setHours(date.getHours() - 7);
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
            } catch (e) {
                return dateStr;
            }
        }
        
        // Build HTML content
        const html = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-primary mb-3"><i class="material-icons-outlined me-2" style="font-size: 18px;">assignment</i> Informasi Umum</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Task Number:</td>
                                    <td><strong>${sap.task_number || 'N/A'}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jenis Laporan:</td>
                                    <td><span class="badge bg-primary">${sap.jenis_laporan || 'N/A'}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Aktivitas Pekerjaan:</td>
                                    <td>${sap.aktivitas_pekerjaan || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Pelaporan:</td>
                                    <td>${formatDate(sap.tanggal_pelaporan || sap.detected_at)}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-success mb-3"><i class="material-icons-outlined me-2" style="font-size: 18px;">location_on</i> Lokasi</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Lokasi:</td>
                                    <td>${sap.lokasi || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Detail Lokasi:</td>
                                    <td>${sap.detail_lokasi || 'N/A'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-info mb-3"><i class="material-icons-outlined me-2" style="font-size: 18px;">description</i> Keterangan</h6>
                            <p class="mb-0">${sap.keterangan || 'Tidak ada keterangan'}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-warning mb-3"><i class="material-icons-outlined me-2" style="font-size: 18px;">person</i> Pelapor</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Nama:</td>
                                    <td><strong>${sap.nama_pelapor || sap.pelapor || 'N/A'}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">SID:</td>
                                    <td>${sap.sid_pelapor || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NIK:</td>
                                    <td>${sap.nik_pelapor || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jabatan:</td>
                                    <td>${sap.jabatan_fungsional_pelapor || sap.jabatan_fungsional_karyawan_pelapor || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jabatan Struktural:</td>
                                    <td>${sap.jabatan_struktural_pelapor || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Departemen:</td>
                                    <td>${sap.departemen_pelapor || sap.departement_pelapor_karyawan || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Divisi:</td>
                                    <td>${sap.divisi_pelapor || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Perusahaan:</td>
                                    <td>${sap.nama_perusahaan_pelapor_karyawan || sap.perusahaan_pelapor || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td><span class="badge ${sap.status_karyawan_pelapor === 'AKTIF' ? 'bg-success' : 'bg-secondary'}">${sap.status_karyawan_pelapor || 'N/A'}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-danger mb-3"><i class="material-icons-outlined me-2" style="font-size: 18px;">contact_mail</i> PIC</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Nama:</td>
                                    <td><strong>${sap.pic || 'N/A'}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">SID:</td>
                                    <td>${sap.sid_pic || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jabatan:</td>
                                    <td>${sap.jabatan_fungsional_pic || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Departemen:</td>
                                    <td>${sap.departemen_pic || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Perusahaan:</td>
                                    <td>${sap.perusahaan_pic || 'N/A'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                ${sap.url_foto ? `
                <div class="col-12 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="material-icons-outlined me-2" style="font-size: 18px;">photo</i> Foto</h6>
                            <a href="${sap.url_foto}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="material-icons-outlined me-1" style="font-size: 16px;">open_in_new</i> Lihat Foto
                            </a>
                        </div>
                    </div>
                </div>
                ` : ''}
                
                ${sap.tools_pengawasan ? `
                <div class="col-md-6 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="material-icons-outlined me-2" style="font-size: 18px;">build</i> Tools Pengawasan</h6>
                            <p class="mb-0">${sap.tools_pengawasan}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
                
                ${sap.catatan_tindakan ? `
                <div class="col-md-6 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="material-icons-outlined me-2" style="font-size: 18px;">note</i> Catatan Tindakan</h6>
                            <p class="mb-0">${sap.catatan_tindakan}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
        `;
        
        modalBody.innerHTML = html;
    }
    
    // Variable to store selected area kerja for SAP filtering
    let selectedAreaKerjaForSap = null;
    let currentAreaKerjaLokasi = null; // Store lokasi name for filtering
    
    // Function to filter SAP by area CCTV
    window.filterSapByAreaCctv = function(cctvNo, cctvName, cctvLokasi) {
        // Close popup
        popupOverlay.setPosition(undefined);
        
        // Store lokasi for filtering
        currentAreaKerjaLokasi = cctvLokasi || cctvName;
        
        // Show modal untuk pilih week
        const modalHtml = `
            <div class="p-3">
                <h6 class="mb-3">Filter SAP di Area CCTV: <strong>${cctvName}</strong></h6>
                <p class="text-muted small mb-3">Lokasi: ${cctvLokasi || 'N/A'}</p>
                <div class="mb-3">
                    <label class="form-label">Pilih Week:</label>
                    <input type="week" id="areaCctvSapWeekFilter" class="form-control">
                </div>
                <div class="mb-3">
                    <small class="text-muted" id="areaCctvSapWeekRange">Week: -</small>
                </div>
                <button class="btn btn-primary w-100" onclick="applyAreaCctvSapFilter()">
                    <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">search</i> Filter SAP
                </button>
            </div>
        `;
        
        Swal.fire({
            title: 'Filter SAP per Area CCTV',
            html: modalHtml,
            showCancelButton: true,
            confirmButtonText: 'Filter',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            didOpen: () => {
                // Set default week (minggu ini)
                const today = new Date();
                const monday = new Date(today);
                monday.setDate(today.getDate() - (today.getDay() === 0 ? 6 : today.getDay() - 1));
                const year = monday.getFullYear();
                const week = getWeekNumber(monday);
                const weekValue = `${year}-W${String(week).padStart(2, '0')}`;
                
                const weekFilter = document.getElementById('areaCctvSapWeekFilter');
                if (weekFilter) {
                    weekFilter.value = weekValue;
                    updateAreaCctvSapWeekRange();
                    
                    weekFilter.addEventListener('change', function() {
                        updateAreaCctvSapWeekRange();
                    });
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                applyAreaCctvSapFilter();
            }
        });
    }
    
    // Function to update week range display for area CCTV SAP filter
    function updateAreaCctvSapWeekRange() {
        const weekFilter = document.getElementById('areaCctvSapWeekFilter');
        const weekRange = document.getElementById('areaCctvSapWeekRange');
        if (!weekFilter || !weekRange) return;
        
        const weekValue = weekFilter.value;
        if (!weekValue) {
            weekRange.textContent = 'Week: -';
            return;
        }
        
        const [year, week] = weekValue.split('-W');
        const weekStart = getDateOfISOWeek(parseInt(week), parseInt(year));
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const startStr = `${weekStart.getDate()} ${months[weekStart.getMonth()]} ${weekStart.getFullYear()}`;
        const endStr = `${weekEnd.getDate()} ${months[weekEnd.getMonth()]} ${weekEnd.getFullYear()}`;
        
        weekRange.textContent = `Week: ${startStr} - ${endStr}`;
    }
    
    // Function to apply SAP filter by area CCTV
    window.applyAreaCctvSapFilter = function() {
        if (!currentAreaKerjaLokasi) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Lokasi area CCTV tidak ditemukan',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        const weekFilter = document.getElementById('areaCctvSapWeekFilter');
        if (!weekFilter || !weekFilter.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih week terlebih dahulu',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        const weekValue = weekFilter.value;
        const [year, week] = weekValue.split('-W');
        const weekStart = getDateOfISOWeek(parseInt(week), parseInt(year));
        weekStart.setHours(0, 0, 0, 0);
        
        const yearStr = weekStart.getFullYear();
        const monthStr = String(weekStart.getMonth() + 1).padStart(2, '0');
        const dayStr = String(weekStart.getDate()).padStart(2, '0');
        const weekStartStr = `${yearStr}-${monthStr}-${dayStr} 00:00:00`;
        
        // Show loading
        Swal.fire({
            title: 'Memuat SAP',
            html: `Memfilter SAP di area CCTV <strong>${currentAreaKerjaLokasi}</strong> untuk week yang dipilih...`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Load SAP data for the week
        fetch(`{{ route('maps.api.filtered-data') }}?week_start=${encodeURIComponent(weekStartStr)}&show_sap=true&show_hazard=true`)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success && data.data && (data.data.sap || data.data.hazard)) {
                    const allSapData = data.data.sap || data.data.hazard || [];
                    
                    // Filter SAP berdasarkan lokasi atau detail_lokasi yang cocok dengan area CCTV
                    const filteredSap = allSapData.filter(sap => {
                        const sapLokasi = (sap.lokasi || '').toLowerCase().trim();
                        const sapDetailLokasi = (sap.detail_lokasi || '').toLowerCase().trim();
                        const areaCctvLokasi = currentAreaKerjaLokasi.toLowerCase().trim();
                        
                        // Check if SAP lokasi or detail_lokasi contains area CCTV lokasi or vice versa
                        const lokasiMatch = sapLokasi.includes(areaCctvLokasi) || areaCctvLokasi.includes(sapLokasi);
                        const detailLokasiMatch = sapDetailLokasi.includes(areaCctvLokasi) || areaCctvLokasi.includes(sapDetailLokasi);
                        const exactMatch = sapLokasi === areaCctvLokasi || sapDetailLokasi === areaCctvLokasi;
                        
                        return lokasiMatch || detailLokasiMatch || exactMatch;
                    });
                    
                    console.log(`Found ${filteredSap.length} SAP items in area CCTV out of ${allSapData.length} total`);
                    
                    // Simpan semua data hasil filter untuk count di tab
                    sapDataAllWeek = [...filteredSap];
                    
                    // Urutkan semua data berdasarkan tanggal terbaru
                    const sortedSapAll = [...filteredSap].sort((a, b) => {
                        const dateA = new Date(a.tanggal_pelaporan || a.detected_at || 0);
                        const dateB = new Date(b.tanggal_pelaporan || b.detected_at || 0);
                        return dateB - dateA; // Terbaru di atas
                    });
                    
                    // Filter data hari ini untuk sidebar
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const todayStr = today.toISOString().split('T')[0];
                    
                    const sapDataToday = sortedSapAll.filter(sap => {
                        if (!sap.tanggal_pelaporan && !sap.detected_at) return false;
                        try {
                            const sapDate = new Date(sap.tanggal_pelaporan || sap.detected_at);
                            sapDate.setHours(0, 0, 0, 0);
                            const sapDateStr = sapDate.toISOString().split('T')[0];
                            return sapDateStr === todayStr;
                        } catch (e) {
                            return false;
                        }
                    });
                    
                    // Map: Hanya tampilkan 1000 data terbaru dari semua data hasil filter
                    const sapDataForMap = sortedSapAll.slice(0, 1000);
                    
                    // Update sidebar dengan data hari ini saja
                    filteredSidebarData.sap = sapDataToday;
                    sapDataForSidebar = sapDataToday;
                    sapData = sapDataForMap; // Untuk map, hanya 1000 terbaru
                    
                    // Update tab counts (menggunakan semua data hasil filter untuk count)
                    updateTabCounts();
                    
                    // Switch to SAP tab
                    currentSidebarTab = 'sap';
                    document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
                    const sapTab = document.querySelector('[data-tab="sap"]');
                    if (sapTab) {
                        sapTab.classList.add('active');
                    }
                    
                    // Render SAP list (tampilkan hanya data hari ini)
                    renderSidebarTab('sap');
                    
                    // Update map markers (hanya 1000 terbaru)
                    updateSapMarkersOnMap(sapDataForMap);
                    
                    // Update evaluation alerts if enabled
                    if (evaluationEnabled) {
                        showEvaluationAlerts();
                    }
                    
                    console.log(`Total filtered: ${sapDataAllWeek.length} SAP items | Sidebar (today): ${sapDataToday.length} SAP items | Map: ${sapDataForMap.length} SAP markers (limited to 1000)`);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Ditemukan ${filteredSap.length} SAP di area CCTV untuk week yang dipilih`,
                        confirmButtonColor: '#3085d6',
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Data',
                        text: 'Tidak ada data SAP untuk week yang dipilih',
                        confirmButtonColor: '#3085d6'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error loading SAP data:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data SAP',
                    confirmButtonColor: '#3085d6'
                });
            });
    }
    
    // Function to filter SAP by area kerja
    window.filterSapByAreaKerja = function(areaKerjaId, lokasiName) {
        // Close popup
        popupOverlay.setPosition(undefined);
        
        // Find area kerja feature from all area kerja layers
        let areaKerjaFeature = null;
        
        // First, try to find in areaKerjaBmo2PamaLayer
        if (areaKerjaBmo2PamaLayer) {
            const areaKerjaFeatures = areaKerjaBmo2PamaLayer.getSource().getFeatures();
            areaKerjaFeature = areaKerjaFeatures.find(f => {
                const props = f.getProperties();
                // Check by id_lokasi, fid, or lokasi
                return props.id_lokasi === areaKerjaId || 
                       props.fid === areaKerjaId || 
                       props.lokasi === areaKerjaId ||
                       props.lokasi === lokasiName;
            });
        }
        
        // If not found, search in all area kerja layers
        if (!areaKerjaFeature && window.areaKerjaLayers && window.areaKerjaLayers.length > 0) {
            for (const layer of window.areaKerjaLayers) {
                if (layer && layer.getSource()) {
                    const features = layer.getSource().getFeatures();
                    areaKerjaFeature = features.find(f => {
                        const props = f.getProperties();
                        // Check by id_lokasi, fid, or lokasi
                        return props.id_lokasi === areaKerjaId || 
                               props.fid === areaKerjaId || 
                               props.lokasi === areaKerjaId ||
                               props.lokasi === lokasiName;
                    });
                    if (areaKerjaFeature) break;
                }
            }
        }
        
        if (!areaKerjaFeature) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Area Kerja tidak ditemukan',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        if (!areaKerjaFeature) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Area Kerja tidak ditemukan',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Store selected area kerja
        selectedAreaKerjaForSap = {
            id: areaKerjaId,
            name: lokasiName,
            feature: areaKerjaFeature
        };
        
        // Store lokasi name for filtering
        currentAreaKerjaLokasi = lokasiName;
        
        // Show modal untuk pilih week
        showAreaKerjaSapFilterModal(areaKerjaFeature, lokasiName);
    }
    
    // Function to show modal for selecting week filter for area kerja SAP
    function showAreaKerjaSapFilterModal(areaKerjaFeature, lokasiName) {
        // Get geometry from feature
        const geometry = areaKerjaFeature.getGeometry();
        if (!geometry) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Geometry area kerja tidak ditemukan',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Get polygon coordinates
        const coordinates = geometry.getCoordinates();
        let polygonCoords = [];
        
        // Handle MultiPolygon or Polygon
        if (coordinates[0][0] && Array.isArray(coordinates[0][0][0])) {
            // MultiPolygon - use first polygon
            polygonCoords = coordinates[0][0];
        } else if (coordinates[0] && Array.isArray(coordinates[0][0])) {
            // Polygon
            polygonCoords = coordinates[0];
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Format koordinat area kerja tidak valid',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        // Convert to EPSG:4326 if needed
        const polygonCoords4326 = polygonCoords.map(coord => {
            return ol.proj.toLonLat(coord, map.getView().getProjection());
        });
        
        // Store polygon coordinates globally
        currentAreaKerjaPolygonCoords = polygonCoords4326;
        
        // Show modal with week selector
        const modalHtml = `
            <div class="p-3">
                <h6 class="mb-3">Filter SAP di Area Kerja: <strong>${lokasiName}</strong></h6>
                <div class="mb-3">
                    <label class="form-label">Pilih Week:</label>
                    <input type="week" id="areaKerjaSapWeekFilter" class="form-control">
                </div>
                <div class="mb-3">
                    <small class="text-muted" id="areaKerjaSapWeekRange">Week: -</small>
                </div>
                <button class="btn btn-primary w-100" onclick="applyAreaKerjaSapFilter()">
                    <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">search</i> Filter SAP
                </button>
            </div>
        `;
        
        Swal.fire({
            title: 'Filter SAP per Area Kerja',
            html: modalHtml,
            showCancelButton: true,
            confirmButtonText: 'Filter',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            didOpen: () => {
                // Set default week (minggu ini)
                const today = new Date();
                const monday = new Date(today);
                monday.setDate(today.getDate() - (today.getDay() === 0 ? 6 : today.getDay() - 1));
                const year = monday.getFullYear();
                const week = getWeekNumber(monday);
                const weekValue = `${year}-W${String(week).padStart(2, '0')}`;
                
                const weekFilter = document.getElementById('areaKerjaSapWeekFilter');
                if (weekFilter) {
                    weekFilter.value = weekValue;
                    updateAreaKerjaSapWeekRange();
                    
                    weekFilter.addEventListener('change', function() {
                        updateAreaKerjaSapWeekRange();
                    });
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                applyAreaKerjaSapFilter();
            }
        });
    }
    
    // Function to update week range display for area kerja SAP filter
    function updateAreaKerjaSapWeekRange() {
        const weekFilter = document.getElementById('areaKerjaSapWeekFilter');
        const weekRange = document.getElementById('areaKerjaSapWeekRange');
        if (!weekFilter || !weekRange) return;
        
        const weekValue = weekFilter.value;
        if (!weekValue) {
            weekRange.textContent = 'Week: -';
            return;
        }
        
        const [year, week] = weekValue.split('-W');
        const weekStart = getDateOfISOWeek(parseInt(week), parseInt(year));
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const startStr = `${weekStart.getDate()} ${months[weekStart.getMonth()]} ${weekStart.getFullYear()}`;
        const endStr = `${weekEnd.getDate()} ${months[weekEnd.getMonth()]} ${weekEnd.getFullYear()}`;
        
        weekRange.textContent = `Week: ${startStr} - ${endStr}`;
    }
    
    // Function to apply SAP filter by area kerja
    window.applyAreaKerjaSapFilter = function() {
        if (!currentAreaKerjaLokasi) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Lokasi area kerja tidak ditemukan',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        const weekFilter = document.getElementById('areaKerjaSapWeekFilter');
        if (!weekFilter || !weekFilter.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih week terlebih dahulu',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        const weekValue = weekFilter.value;
        const [year, week] = weekValue.split('-W');
        const weekStart = getDateOfISOWeek(parseInt(week), parseInt(year));
        weekStart.setHours(0, 0, 0, 0);
        
        const yearStr = weekStart.getFullYear();
        const monthStr = String(weekStart.getMonth() + 1).padStart(2, '0');
        const dayStr = String(weekStart.getDate()).padStart(2, '0');
        const weekStartStr = `${yearStr}-${monthStr}-${dayStr} 00:00:00`;
        
        // Show loading
        Swal.fire({
            title: 'Memuat SAP',
            html: `Memfilter SAP di area kerja <strong>${currentAreaKerjaLokasi}</strong> untuk week yang dipilih...`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Load SAP data for the week
        fetch(`{{ route('maps.api.filtered-data') }}?week_start=${encodeURIComponent(weekStartStr)}&show_sap=true&show_hazard=true`)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success && data.data && (data.data.sap || data.data.hazard)) {
                    const allSapData = data.data.sap || data.data.hazard || [];
                    
                    // Filter SAP berdasarkan lokasi atau detail_lokasi yang cocok dengan area kerja
                    const filteredSap = allSapData.filter(sap => {
                        const sapLokasi = (sap.lokasi || '').toLowerCase().trim();
                        const sapDetailLokasi = (sap.detail_lokasi || '').toLowerCase().trim();
                        const areaKerjaLokasi = currentAreaKerjaLokasi.toLowerCase().trim();
                        
                        // Check if SAP lokasi or detail_lokasi contains area kerja lokasi or vice versa
                        // Support partial matching untuk fleksibilitas
                        const lokasiMatch = sapLokasi.includes(areaKerjaLokasi) || areaKerjaLokasi.includes(sapLokasi);
                        const detailLokasiMatch = sapDetailLokasi.includes(areaKerjaLokasi) || areaKerjaLokasi.includes(sapDetailLokasi);
                        
                        // Also check exact match
                        const exactMatch = sapLokasi === areaKerjaLokasi || sapDetailLokasi === areaKerjaLokasi;
                        
                        return lokasiMatch || detailLokasiMatch || exactMatch;
                    });
                    
                    console.log(`Found ${filteredSap.length} SAP items in area kerja out of ${allSapData.length} total`);
                    
                    // Simpan semua data hasil filter untuk count di tab
                    sapDataAllWeek = [...filteredSap];
                    
                    // Urutkan semua data berdasarkan tanggal terbaru
                    const sortedSapAll = [...filteredSap].sort((a, b) => {
                        const dateA = new Date(a.tanggal_pelaporan || a.detected_at || 0);
                        const dateB = new Date(b.tanggal_pelaporan || b.detected_at || 0);
                        return dateB - dateA; // Terbaru di atas
                    });
                    
                    // Filter data hari ini untuk sidebar
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const todayStr = today.toISOString().split('T')[0];
                    
                    const sapDataToday = sortedSapAll.filter(sap => {
                        if (!sap.tanggal_pelaporan && !sap.detected_at) return false;
                        try {
                            const sapDate = new Date(sap.tanggal_pelaporan || sap.detected_at);
                            sapDate.setHours(0, 0, 0, 0);
                            const sapDateStr = sapDate.toISOString().split('T')[0];
                            return sapDateStr === todayStr;
                        } catch (e) {
                            return false;
                        }
                    });
                    
                    // Map: Hanya tampilkan 1000 data terbaru dari semua data hasil filter
                    const sapDataForMap = sortedSapAll.slice(0, 1000);
                    
                    // Update sidebar dengan data hari ini saja
                    filteredSidebarData.sap = sapDataToday;
                    sapDataForSidebar = sapDataToday;
                    sapData = sapDataForMap; // Untuk map, hanya 1000 terbaru
                    
                    // Update tab counts (menggunakan semua data hasil filter untuk count)
                    updateTabCounts();
                    
                    // Switch to SAP tab
                    currentSidebarTab = 'sap';
                    document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
                    const sapTab = document.querySelector('[data-tab="sap"]');
                    if (sapTab) {
                        sapTab.classList.add('active');
                    }
                    
                    // Render SAP list (tampilkan hanya data hari ini)
                    renderSidebarTab('sap');
                    
                    // Update map markers (hanya 1000 terbaru)
                    updateSapMarkersOnMap(sapDataForMap);
                    
                    // Update evaluation alerts if enabled
                    if (evaluationEnabled) {
                        showEvaluationAlerts();
                    }
                    
                    console.log(`Total filtered: ${sapDataAllWeek.length} SAP items | Sidebar (today): ${sapDataToday.length} SAP items | Map: ${sapDataForMap.length} SAP markers (limited to 1000)`);
                    
                    // Highlight area kerja
                    if (selectedAreaKerjaForSap && selectedAreaKerjaForSap.feature) {
                        highlightAreaKerjaForSap(selectedAreaKerjaForSap.feature);
                    }
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Ditemukan ${filteredSap.length} SAP di area kerja untuk week yang dipilih`,
                        confirmButtonColor: '#3085d6',
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Data',
                        text: 'Tidak ada data SAP untuk week yang dipilih',
                        confirmButtonColor: '#3085d6'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error loading SAP data:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data SAP',
                    confirmButtonColor: '#3085d6'
                });
            });
    }
    
    // Function to highlight area kerja for SAP
    function highlightAreaKerjaForSap(areaKerjaFeature) {
        // Remove existing highlight
        if (highlightedAreaKerjaLayer) {
            map.removeLayer(highlightedAreaKerjaLayer);
            highlightedAreaKerjaLayer = null;
        }
        
        // Create highlight layer
        const highlightSource = new ol.source.Vector({
            features: [areaKerjaFeature.clone()]
        });
        
        highlightedAreaKerjaLayer = new ol.layer.Vector({
            source: highlightSource,
            style: function(feature) {
                const props = feature.getProperties();
                const areaKerja = props.area_kerja || '';
                
                let fillColor = 'rgba(59, 130, 246, 0.4)';
                let strokeColor = '#3b82f6';
                let strokeWidth = 3;
                
                if (areaKerja === 'Pit') {
                    fillColor = 'rgba(239, 68, 68, 0.4)';
                    strokeColor = '#ef4444';
                } else if (areaKerja === 'Hauling') {
                    fillColor = 'rgba(245, 158, 11, 0.4)';
                    strokeColor = '#f59e0b';
                } else if (areaKerja === 'Infra Tambang') {
                    fillColor = 'rgba(59, 130, 246, 0.4)';
                    strokeColor = '#3b82f6';
                }
                
                return new ol.style.Style({
                    fill: new ol.style.Fill({ color: fillColor }),
                    stroke: new ol.style.Stroke({
                        color: strokeColor,
                        width: strokeWidth
                    })
                });
            },
            zIndex: 2000
        });
        
        map.addLayer(highlightedAreaKerjaLayer);
        
        // Fit map to show area kerja
        const extent = highlightedAreaKerjaLayer.getSource().getExtent();
        map.getView().fit(extent, {
            padding: [50, 50, 50, 50],
            duration: 500
        });
    }

    function showInsidenPopup(coordinate, insiden) {
        if (!insiden) {
            return;
        }

        // Ensure area kerja layers remain visible when showing insiden popup
        if (window.areaKerjaLayers && Array.isArray(window.areaKerjaLayers)) {
            window.areaKerjaLayers.forEach(layer => {
                if (layer) {
                    layer.setVisible(true);
                    layer.setOpacity(1.0);
                }
            });
        }
        if (areaKerjaBmo2PamaLayer) {
            areaKerjaBmo2PamaLayer.setVisible(true);
            areaKerjaBmo2PamaLayer.setOpacity(1.0);
        }
        // Force map render to ensure area kerja layers are visible
        if (map) {
            map.render();
        }

        const escapedNo = insiden.no_kecelakaan ? insiden.no_kecelakaan.replace(/"/g, '&quot;') : '';
        const content = `
            <div style="min-width: 220px; background-color: #ffffff !important;">
                <h6 style="margin: 0 0 8px 0;">${insiden.no_kecelakaan}</h6>
                <p style="margin: 5px 0; font-size: 13px; background-color: #ffffff !important;">
                    <strong>Site:</strong> ${insiden.site || 'N/A'}<br>
                    <strong>Layer:</strong> ${insiden.layer || 'N/A'}<br>
                    <strong>Kategori:</strong> ${insiden.kategori || 'N/A'}<br>
                    <strong>Status LPI:</strong> ${insiden.status_lpi || 'N/A'}
                </p>
                <button class="btn btn-sm btn-primary w-100" data-no-kec="${escapedNo}" onclick="openInsidenModal(this.dataset.noKec)">
                    Detail Insiden
                </button>
            </div>
        `;

        document.getElementById('popup-content').innerHTML = content;
        popupOverlay.setPosition(coordinate);
    }

    // Function to populate site filter dropdown - ambil dari database
    function populateSiteFilter() {
        if (!siteFilter) {
            return;
        }

        // Ambil data site dari database melalui API
        fetch('{{ route("hazard-detection.api.sites-list") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    // Populate dropdown dengan data dari database
                    siteFilter.innerHTML = '<option value="">Semua Site</option>';
                    data.data.forEach(function(site) {
                        if (site && site.trim()) {
                            const option = document.createElement('option');
                            option.value = site.trim();
                            option.textContent = site.trim();
                            siteFilter.appendChild(option);
                        }
                    });
                } else {
                    // Fallback: ambil dari data lokal jika API gagal
                    const sites = new Set();
                    
                    // From CCTV locations (fallback)
                    cctvLocations.forEach(function(cctv) {
                        if (cctv.site) {
                            sites.add(cctv.site);
                        }
                    });
                    
                    // From hazard detections
                    hazardDetections.forEach(function(hazard) {
                        if (hazard.site || hazard.nama_site) {
                            sites.add(hazard.site || hazard.nama_site);
                        }
                    });
                    
                    // From insiden dataset
                    insidenDataset.forEach(function(insiden) {
                        if (insiden.site) {
                            sites.add(insiden.site);
                        }
                    });
                    
                    // Sort sites alphabetically
                    const sortedSites = Array.from(sites).sort();
                    
                    // Populate dropdown
                    siteFilter.innerHTML = '<option value="">Semua Site</option>';
                    sortedSites.forEach(function(site) {
                        const option = document.createElement('option');
                        option.value = site;
                        option.textContent = site;
                        siteFilter.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading sites from database:', error);
                // Fallback: ambil dari data lokal jika API error
                const sites = new Set();
                
                cctvLocations.forEach(function(cctv) {
                    if (cctv.site) {
                        sites.add(cctv.site);
                    }
                });
                
                hazardDetections.forEach(function(hazard) {
                    if (hazard.site || hazard.nama_site) {
                        sites.add(hazard.site || hazard.nama_site);
                    }
                });
                
                insidenDataset.forEach(function(insiden) {
                    if (insiden.site) {
                        sites.add(insiden.site);
                    }
                });
                
                const sortedSites = Array.from(sites).sort();
                siteFilter.innerHTML = '<option value="">Semua Site</option>';
                sortedSites.forEach(function(site) {
                    const option = document.createElement('option');
                    option.value = site;
                    option.textContent = site;
                    siteFilter.appendChild(option);
                });
            });
    }

    // Function to update statistics based on site filter
    function updateStatisticsBySite(site) {
        // Filter data based on site
        let filteredHazards = hazardDetections;
        let filteredCctv = cctvLocations;
        let filteredInsiden = insidenDataset;
        
        if (site) {
            // Normalize site name for comparison (trim, uppercase)
            const normalizedSite = site.trim().toUpperCase();
            
            filteredHazards = hazardDetections.filter(function(h) {
                const hazardSite = (h.site || h.nama_site || '').toString().trim().toUpperCase();
                return hazardSite === normalizedSite;
            });
            
            filteredCctv = cctvLocations.filter(function(c) {
                const cctvSite = (c.site || '').toString().trim().toUpperCase();
                return cctvSite === normalizedSite;
            });
            
            filteredInsiden = insidenDataset.filter(function(i) {
                const insidenSite = (i.site || '').toString().trim();
                if (!insidenSite) return false;
                
                // Normalize both site names: remove spaces, dashes, and convert to uppercase
                const normalizeSiteName = function(siteName) {
                    if (!siteName) return '';
                    return siteName.toString().trim()
                        .toUpperCase()
                        .replace(/\s+/g, '')  // Remove all spaces
                        .replace(/[^A-Z0-9]/g, ''); // Remove special characters except letters and numbers
                };
                
                const normalizedInsidenSite = normalizeSiteName(insidenSite);
                const normalizedFilterSite = normalizeSiteName(normalizedSite);
                
                // Debug logging (can be removed in production)
                // console.log('Filtering insiden:', {
                //     originalSite: insidenSite,
                //     normalizedInsidenSite: normalizedInsidenSite,
                //     filterSite: normalizedSite,
                //     normalizedFilterSite: normalizedFilterSite,
                //     match: normalizedInsidenSite === normalizedFilterSite || 
                //            normalizedInsidenSite.includes(normalizedFilterSite) || 
                //            normalizedFilterSite.includes(normalizedInsidenSite)
                // });
                
                // Check if normalized sites match exactly
                if (normalizedInsidenSite === normalizedFilterSite) {
                    return true;
                }
                
                // Check if one contains the other (for partial matches)
                // This handles cases like "BMO2" matching "BMO2PAMA" or "BMO2" matching "BMO2"
                if (normalizedInsidenSite.includes(normalizedFilterSite) || 
                    normalizedFilterSite.includes(normalizedInsidenSite)) {
                    return true;
                }
                
                // Additional check: extract base name and number for better matching
                // This handles "BMO2" matching "BMO 2", "BMO-2", "BMO2 PAMA", etc.
                const extractBaseAndNumber = function(site) {
                    // Match pattern like "BMO2", "BMO 2", "BMO-2", etc.
                    const match = site.match(/^([A-Z]+)(\d+)/);
                    if (match) {
                        return { base: match[1], number: match[2] };
                    }
                    return null;
                };
                
                const insidenParts = extractBaseAndNumber(normalizedInsidenSite);
                const filterParts = extractBaseAndNumber(normalizedFilterSite);
                
                if (insidenParts && filterParts) {
                    // Match if base name and number are the same
                    // e.g., "BMO" + "2" matches "BMO" + "2"
                    if (insidenParts.base === filterParts.base && 
                        insidenParts.number === filterParts.number) {
                        return true;
                    }
                }
                
                return false;
            });
            
            // Debug: log filtered results
            // console.log('Filtered insiden for site "' + site + '":', filteredInsiden.length, 'of', insidenDataset.length);
        }
        
        // Calculate HAZARD statistics
        const hazardCount = filteredHazards.length;
        const activeHazards = filteredHazards.filter(function(h) {
            return h.status === 'active';
        }).length;
        const resolvedHazards = filteredHazards.filter(function(h) {
            return h.status === 'resolved';
        }).length;
        
        // Calculate INSIDEN statistics
        const insidenCount = filteredInsiden.length;
        
        // Calculate GR (Golden Rules) statistics - count hazards with golden rule
        const grCount = filteredHazards.filter(function(h) {
            return h.nama_goldenrule && h.nama_goldenrule !== 'N/A' && h.nama_goldenrule !== '';
        }).length;
        
        // Calculate totals for percentage calculation
        const totalHazards = hazardDetections.length;
        const totalInsiden = insidenDataset.length;
        const totalGr = hazardDetections.filter(function(h) {
            return h.nama_goldenrule && h.nama_goldenrule !== 'N/A' && h.nama_goldenrule !== '';
        }).length;
        
        // Calculate percentages for donut charts
        // Percentage shows how much of the filtered data represents from total data
        const hazardPercentage = totalHazards > 0 ? Math.round((hazardCount / totalHazards) * 100) : 100;
        const insidenPercentage = totalInsiden > 0 ? Math.round((insidenCount / totalInsiden) * 100) : 100;
        const grPercentage = totalGr > 0 ? Math.round((grCount / totalGr) * 100) : 100;
        
        // Update HAZARD display with animation
        const statHazardCount = document.getElementById('statHazardCount');
        const statHazardChange = document.getElementById('statHazardChange');
        const statHazardText = document.getElementById('statHazardText');
        if (statHazardCount) {
            animateNumber('statHazardCount', hazardCount, 800);
        }
        if (statHazardChange) {
            // Calculate percentage of total (or use a default calculation)
            // For now, using percentage of active vs total filtered
            const percentage = hazardCount > 0 ? ((activeHazards / hazardCount) * 100).toFixed(1) : '0.0';
            statHazardChange.textContent = percentage + '%';
        }
        if (statHazardText) {
            statHazardText.textContent = hazardCount + ' hazards';
        }
        
        // TBC (To Be Concerned) - tidak diupdate dengan data CCTV
        // TBC adalah data statis dari database hazard_validations, tetap menggunakan data awal dari PHP
        // Tidak perlu mengupdate TBC karena data sudah benar dari server-side
        
        // Update INSIDEN display with animation
        const statInsidenCount = document.getElementById('statInsidenCount');
        const statInsidenChange = document.getElementById('statInsidenChange');
        const statInsidenText = document.getElementById('statInsidenText');
        if (statInsidenCount) {
            animateNumber('statInsidenCount', insidenCount, 800);
        }
        if (statInsidenChange) {
            // Percentage of total insiden
            const percentage = totalInsiden > 0 ? ((insidenCount / totalInsiden) * 100).toFixed(1) : '0.0';
            statInsidenChange.textContent = percentage + '%';
        }
        if (statInsidenText) {
            statInsidenText.textContent = insidenCount + ' insiden';
        }
        
        // Update GR display with animation
        const statGrCount = document.getElementById('statGrCount');
        const statGrChange = document.getElementById('statGrChange');
        const statGrText = document.getElementById('statGrText');
        if (statGrCount) {
            animateNumber('statGrCount', grCount, 800);
        }
        if (statGrChange) {
            // Percentage of total GR
            const percentage = totalGr > 0 ? ((grCount / totalGr) * 100).toFixed(1) : '0.0';
            statGrChange.textContent = percentage + '%';
        }
        if (statGrText) {
            statGrText.textContent = grCount + ' golden rules';
        }
        
        // Update donut charts
        updateDonutChart('donutHazard', hazardPercentage, '#0d6efd');
        // Donut chart CCTV akan diupdate di dalam fetch API untuk konsistensi dengan data database
        updateDonutChart('donutInsiden', insidenPercentage, '#fd7e14');
        updateDonutChart('donutGr', grPercentage, '#20c997');
    }

    // Function to update donut chart
    // Store current percentage for each donut chart for animation
    const donutChartState = {
        donutHazard: 0,
        donutCctv: 0,
        donutInsiden: 0,
        donutGr: 0
    };
    
    // Store animation frame IDs to cancel if needed
    const donutAnimationFrames = {};
    
    // Store current values for number animation
    const numberAnimationState = {
        statHazardCount: 0,
        statCctvCount: 0,
        statInsidenCount: 0,
        statGrCount: 0
    };
    
    // Store animation frame IDs for number animations
    const numberAnimationFrames = {};
    
    // Function to animate number with smooth transition
    // Updated to support multiple elements with the same ID
    function animateNumber(elementId, targetValue, duration = 800) {
        // Get all elements with this ID (querySelectorAll for multiple elements)
        const elements = document.querySelectorAll(`#${elementId}`);
        if (!elements || elements.length === 0) return;
        
        // Get current value from state or parse from first element
        let currentValue = numberAnimationState[elementId];
        if (currentValue === undefined || currentValue === null) {
            const currentText = elements[0].textContent || '0';
            // Remove formatting (commas, spaces) and parse
            currentValue = parseInt(currentText.replace(/[^\d]/g, '')) || 0;
        }
        
        // Cancel any existing animation for this element
        if (numberAnimationFrames[elementId]) {
            cancelAnimationFrame(numberAnimationFrames[elementId]);
        }
        
        // If values are the same, no need to animate
        if (Math.abs(currentValue - targetValue) < 1) {
            numberAnimationState[elementId] = targetValue;
            const formattedValue = targetValue.toLocaleString('id-ID');
            elements.forEach(el => {
                el.textContent = formattedValue;
            });
            return;
        }
        
        // Animation parameters
        const startTime = performance.now();
        const startValue = currentValue;
        const endValue = targetValue;
        
        // Easing function for smooth animation (ease-out cubic)
        function easeOutCubic(t) {
            return 1 - Math.pow(1 - t, 3);
        }
        
        // Animation function
        function animate(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Apply easing
            const easedProgress = easeOutCubic(progress);
            
            // Calculate current value
            const currentValue = Math.round(startValue + (endValue - startValue) * easedProgress);
            
            // Update all elements with formatted number
            const formattedValue = currentValue.toLocaleString('id-ID');
            elements.forEach(el => {
                el.textContent = formattedValue;
            });
            
            // Continue animation if not finished
            if (progress < 1) {
                numberAnimationFrames[elementId] = requestAnimationFrame(animate);
            } else {
                // Animation complete, update state
                numberAnimationState[elementId] = endValue;
                const finalFormattedValue = endValue.toLocaleString('id-ID');
                elements.forEach(el => {
                    el.textContent = finalFormattedValue;
                });
                delete numberAnimationFrames[elementId];
            }
        }
        
        // Start animation
        numberAnimationFrames[elementId] = requestAnimationFrame(animate);
    }
    
    function updateDonutChart(elementId, targetPercentage, color) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        // Ensure percentage is between 0 and 100
        targetPercentage = Math.max(0, Math.min(100, targetPercentage));
        
        // Get current percentage from state
        const currentPercentage = donutChartState[elementId] || 0;
        
        // Cancel any existing animation for this chart
        if (donutAnimationFrames[elementId]) {
            cancelAnimationFrame(donutAnimationFrames[elementId]);
        }
        
        // If values are the same, no need to animate
        if (Math.abs(currentPercentage - targetPercentage) < 0.1) {
            donutChartState[elementId] = targetPercentage;
            // Still update the chart to ensure it's rendered
            if (typeof $ !== 'undefined' && typeof $.fn.peity !== 'undefined') {
                element.textContent = Math.round(targetPercentage) + '/' + 100;
                if (element._peity) {
                    try {
                        $(element).peity('destroy');
                    } catch(e) {}
                }
                try {
                    $(element).peity('donut', {
                        fill: [color, "rgb(0 0 0 / 10%)"],
                        innerRadius: 32,
                        radius: 40
                    });
                } catch(e) {
                    console.error('Error updating donut chart:', e);
                }
            }
            return;
        }
        
        // Wait for jQuery and peity to be available
        if (typeof $ === 'undefined' || typeof $.fn.peity === 'undefined') {
            setTimeout(function() {
                updateDonutChart(elementId, targetPercentage, color);
            }, 100);
            return;
        }
        
        // Animation parameters
        const duration = 800; // milliseconds
        const startTime = performance.now();
        const startValue = currentPercentage;
        const endValue = targetPercentage;
        
        // Easing function for smooth animation (ease-out cubic)
        function easeOutCubic(t) {
            return 1 - Math.pow(1 - t, 3);
        }
        
        // Animation function
        function animate(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Apply easing
            const easedProgress = easeOutCubic(progress);
            
            // Calculate current value
            const currentValue = startValue + (endValue - startValue) * easedProgress;
            
            // Update the text content for peity
            element.textContent = Math.round(currentValue) + '/' + 100;
            
            // Destroy existing peity chart if exists
            if (element._peity) {
                try {
                    $(element).peity('destroy');
                } catch(e) {
                    // Ignore destroy errors
                }
            }
            
            // Recreate peity chart with current animated value
            try {
                $(element).peity('donut', {
                    fill: [color, "rgb(0 0 0 / 10%)"],
                    innerRadius: 32,
                    radius: 40
                });
            } catch(e) {
                console.error('Error updating donut chart:', e);
            }
            
            // Continue animation if not finished
            if (progress < 1) {
                donutAnimationFrames[elementId] = requestAnimationFrame(animate);
            } else {
                // Animation complete, update state
                donutChartState[elementId] = endValue;
                delete donutAnimationFrames[elementId];
            }
        }
        
        // Start animation
        donutAnimationFrames[elementId] = requestAnimationFrame(animate);
    }
    
    // Make function globally accessible
    window.updateDonutChart = updateDonutChart;

    // Function to filter map features by site
    function filterBySite(site) {
        currentSiteFilter = site || '';
        
        // Trigger style refresh for all layers
        // OpenLayers akan otomatis memanggil style function lagi
        if (hazardLayer) {
            hazardLayer.changed();
        }
        if (cctvLayer) {
            cctvLayer.changed();
        }
        if (insidenLayer) {
            insidenLayer.changed();
        }
        
        // Filter hazard list view
        filterHazardListView(site);
        
        // Update statistics based on site filter
        updateStatisticsBySite(site);
    }

    // Function to filter hazard list view by site
    function filterHazardListView(site) {
        const hazardItems = document.querySelectorAll('.hazard-item');
        hazardItems.forEach(function(item) {
            const hazardId = item.getAttribute('data-hazard-id');
            const hazard = hazardDetections.find(h => h.id === hazardId);
            
            if (!hazard) {
                item.style.display = site ? 'none' : 'block';
                return;
            }
            
            const hazardSite = hazard.site || hazard.nama_site || null;
            if (site) {
                item.style.display = hazardSite === site ? 'block' : 'none';
            } else {
                item.style.display = 'block';
            }
        });
        
        // Filter insiden list view
        const insidenItems = document.querySelectorAll('[data-no-kecelakaan]');
        insidenItems.forEach(function(item) {
            const noKecelakaan = item.getAttribute('data-no-kecelakaan');
            const insiden = insidenDataset.find(i => i.no_kecelakaan === noKecelakaan);
            
            if (!insiden) {
                item.style.display = site ? 'none' : 'block';
                return;
            }
            
            const insidenSite = insiden.site || null;
            if (site) {
                item.style.display = insidenSite === site ? 'block' : 'none';
            } else {
                item.style.display = 'block';
            }
        });
    }

    // Event listener for site filter sudah dipindahkan ke mainFilterSiteDropdown
    // Filter site sekarang terintegrasi dengan dropdown button di card Hazard Location Map

    // Initialize site filter on page load
    setTimeout(function() {
        // populateSiteFilter() sudah tidak diperlukan karena menggunakan loadMainFilterOptions()
        
        // Initialize donut chart states with initial values (100% for all data)
        const totalHazards = hazardDetections.length;
        // TBC - ambil nilai dari elemen HTML yang sudah di-render dari PHP
        const statCctvCountElement = document.getElementById('statCctvCount');
        const totalTbc = statCctvCountElement ? parseInt(statCctvCountElement.textContent.replace(/,/g, '')) || 0 : 0;
        const totalInsiden = insidenDataset.length;
        const totalGr = hazardDetections.filter(function(h) {
            return h.nama_goldenrule && h.nama_goldenrule !== 'N/A' && h.nama_goldenrule !== '';
        }).length;
        
        // Set initial state - TBC tidak perlu animasi karena data statis
        donutChartState.donutHazard = totalHazards > 0 ? 100 : 0;
        donutChartState.donutCctv = 100; // TBC tetap 100% karena data statis
        donutChartState.donutInsiden = totalInsiden > 0 ? 100 : 0;
        donutChartState.donutGr = totalGr > 0 ? 100 : 0;
        
        // Initialize number animation states with initial values
        numberAnimationState.statHazardCount = totalHazards;
        numberAnimationState.statCctvCount = totalTbc; // Gunakan nilai TBC dari HTML
        numberAnimationState.statInsidenCount = totalInsiden;
        numberAnimationState.statGrCount = totalGr;
        
        // Initialize TBC donut chart dengan nilai 100% (data statis)
        updateDonutChart('donutCctv', 100, '#6f42c1');
        
        // Initialize statistics with no filter (all sites)
        updateStatisticsBySite('');
    }, 500);

    function highlightAreaKerjaForCCTV(cctv) {
        // Remove previous highlight
        if (highlightedAreaKerjaLayer) {
            map.removeLayer(highlightedAreaKerjaLayer);
            highlightedAreaKerjaLayer = null;
        }
        
        if (!areaKerjaBmo2PamaLayer) {
            return;
        }
        
        const cctvName = cctv.name || cctv.cctv_name || cctv.nama_cctv || '';
        const cctvNo = cctv.no_cctv || cctv.nomor_cctv || '';
        
        console.log('Searching area kerja for CCTV:', { cctvName, cctvNo, cctv });
        
        const matchingFeatures = [];
        
        // Helper function to normalize CCTV name for matching
        function normalizeCctvName(name) {
            if (!name) return '';
            return name.toLowerCase()
                .replace(/\s+/g, '')
                .replace(/[_-]/g, '')
                .trim();
        }
        
        // Method 1: Search in intersection layer (most accurate)
        if (intersectionBmo2PamaLayer) {
            const intersectionSource = intersectionBmo2PamaLayer.getSource();
            const intersectionFeatures = intersectionSource.getFeatures();
            
            console.log('Checking intersection layer, features:', intersectionFeatures.length);
            
            intersectionFeatures.forEach(function(feature) {
                const props = feature.getProperties();
                const featureCctvName = props.nama_cctv || '';
                const featureCctvNo = props.nomor_cctv || '';
                
                // Normalize names for better matching
                const normalizedCctvName = normalizeCctvName(cctvName);
                const normalizedFeatureName = normalizeCctvName(featureCctvName);
                
                // Match by CCTV name or number (more flexible matching)
                const nameMatch = (normalizedCctvName && normalizedFeatureName && 
                    (normalizedFeatureName.includes(normalizedCctvName) || 
                     normalizedCctvName.includes(normalizedFeatureName)));
                const numberMatch = (cctvNo && featureCctvNo && cctvNo === featureCctvNo);
                const partialMatch = (cctvName && featureCctvName && 
                    (featureCctvName.toLowerCase().includes(cctvName.toLowerCase()) ||
                     cctvName.toLowerCase().includes(featureCctvName.toLowerCase())));
                
                if (nameMatch || numberMatch || partialMatch) {
                    console.log('Found match in intersection:', { featureCctvName, featureCctvNo, props });
                    // Found intersection, now find corresponding area kerja
                    const idLokasi = props.id_lokasi;
                    const lokasi = props.lokasi;
                    
                    if (idLokasi || lokasi) {
                        const areaKerjaSource = areaKerjaBmo2PamaLayer.getSource();
                        const areaKerjaFeatures = areaKerjaSource.getFeatures();
                        
                        areaKerjaFeatures.forEach(function(areaKerjaFeature) {
                            const areaKerjaProps = areaKerjaFeature.getProperties();
                            if ((idLokasi && areaKerjaProps.id_lokasi === idLokasi) ||
                                (lokasi && areaKerjaProps.lokasi === lokasi)) {
                                if (!matchingFeatures.find(f => f === areaKerjaFeature)) {
                                    matchingFeatures.push(areaKerjaFeature);
                                }
                            }
                        });
                    }
                }
            });
        }
        
        // Method 2: Search in area CCTV layer and find overlapping area kerja
        if (matchingFeatures.length === 0 && areaCctvBmo2PamaLayer) {
            const areaCctvSource = areaCctvBmo2PamaLayer.getSource();
            const areaCctvFeatures = areaCctvSource.getFeatures();
            
            let cctvAreaFeature = null;
            areaCctvFeatures.forEach(function(feature) {
                const props = feature.getProperties();
                const featureCctvName = props.nama_cctv || '';
                const featureCctvNo = props.nomor_cctv || '';
                
                    const normalizedCctvName = normalizeCctvName(cctvName);
                    const normalizedFeatureName = normalizeCctvName(featureCctvName);
                    
                    const nameMatch = (normalizedCctvName && normalizedFeatureName && 
                        (normalizedFeatureName.includes(normalizedCctvName) || 
                         normalizedCctvName.includes(normalizedFeatureName)));
                    const numberMatch = (cctvNo && featureCctvNo && cctvNo === featureCctvNo);
                    const partialMatch = (cctvName && featureCctvName && 
                        (featureCctvName.toLowerCase().includes(cctvName.toLowerCase()) ||
                         cctvName.toLowerCase().includes(featureCctvName.toLowerCase())));
                    
                    if (nameMatch || numberMatch || partialMatch) {
                        console.log('Found CCTV area feature:', { featureCctvName, featureCctvNo });
                        cctvAreaFeature = feature;
                    }
            });
            
            // If found CCTV area, find overlapping area kerja
            if (cctvAreaFeature) {
                const cctvAreaGeometry = cctvAreaFeature.getGeometry();
                const areaKerjaSource = areaKerjaBmo2PamaLayer.getSource();
                const areaKerjaFeatures = areaKerjaSource.getFeatures();
                
                areaKerjaFeatures.forEach(function(areaKerjaFeature) {
                    const areaKerjaGeometry = areaKerjaFeature.getGeometry();
                    if (areaKerjaGeometry && cctvAreaGeometry) {
                        // Check if geometries intersect
                        if (areaKerjaGeometry.intersectsExtent(cctvAreaGeometry.getExtent())) {
                            // More precise check: get intersection
                            try {
                                const intersection = areaKerjaGeometry.intersection(cctvAreaGeometry);
                                if (intersection && !intersection.isEmpty()) {
                                    if (!matchingFeatures.find(f => f === areaKerjaFeature)) {
                                        matchingFeatures.push(areaKerjaFeature);
                                    }
                                }
                            } catch(e) {
                                // If intersection fails, use extent check
                                if (!matchingFeatures.find(f => f === areaKerjaFeature)) {
                                    matchingFeatures.push(areaKerjaFeature);
                                }
                            }
                        }
                    }
                });
            }
        }
        
        // Method 3: Use CCTV location point if available
        if (matchingFeatures.length === 0 && cctv.location && Array.isArray(cctv.location) && cctv.location.length === 2) {
            const cctvPoint = ol.proj.fromLonLat(cctv.location);
            const areaKerjaSource = areaKerjaBmo2PamaLayer.getSource();
            const areaKerjaFeatures = areaKerjaSource.getFeatures();
            
            // Find area kerja that contains the CCTV location
            areaKerjaFeatures.forEach(function(feature) {
                const geometry = feature.getGeometry();
                if (geometry && geometry.intersectsCoordinate(cctvPoint)) {
                    if (!matchingFeatures.find(f => f === feature)) {
                        matchingFeatures.push(feature);
                    }
                }
            });
            
            // If no direct intersection, find nearest area kerja within 1000m
            if (matchingFeatures.length === 0) {
                let nearestFeature = null;
                let minDistance = Infinity;
                const cctvLonLat = ol.proj.toLonLat(cctvPoint);
                
                areaKerjaFeatures.forEach(function(feature) {
                    const geometry = feature.getGeometry();
                    if (geometry) {
                        const closestPoint = geometry.getClosestPoint(cctvPoint);
                        const closestLonLat = ol.proj.toLonLat(closestPoint);
                        const distance = ol.sphere.getDistance(cctvLonLat, closestLonLat);
                        
                        if (distance < 1000 && distance < minDistance) {
                            minDistance = distance;
                            nearestFeature = feature;
                        }
                    }
                });
                
                if (nearestFeature) {
                    matchingFeatures.push(nearestFeature);
                }
            }
        }
        
        console.log('Found matching area kerja features:', matchingFeatures.length);
        
        // Create highlight layer with matching features
        if (matchingFeatures.length > 0) {
            const highlightSource = new ol.source.Vector({
                features: matchingFeatures.map(function(feature) {
                    // Clone feature for highlight
                    const clonedFeature = feature.clone();
                    return clonedFeature;
                })
            });
            
            highlightedAreaKerjaLayer = new ol.layer.Vector({
                source: highlightSource,
                style: function(feature) {
                    const props = feature.getProperties();
                    const areaKerja = props.area_kerja || '';
                    
                    // Enhanced highlight style
                    let fillColor = 'rgba(59, 130, 246, 0.5)'; // Blue with more opacity
                    let strokeColor = '#3b82f6';
                    let strokeWidth = 3;
                    
                    if (areaKerja === 'Pit') {
                        fillColor = 'rgba(239, 68, 68, 0.5)'; // Red
                        strokeColor = '#ef4444';
                    } else if (areaKerja === 'Hauling') {
                        fillColor = 'rgba(245, 158, 11, 0.5)'; // Orange
                        strokeColor = '#f59e0b';
                    } else if (areaKerja === 'Infra Tambang') {
                        fillColor = 'rgba(59, 130, 246, 0.5)'; // Blue
                        strokeColor = '#3b82f6';
                    }
                    
                    return new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: fillColor
                        }),
                        stroke: new ol.style.Stroke({
                            color: strokeColor,
                            width: strokeWidth,
                            lineDash: [10, 5] // Dashed line for highlight
                        })
                    });
                },
                zIndex: 1002, // Above CCTV but below hazard markers
                opacity: 0.9
            });
            
            map.addLayer(highlightedAreaKerjaLayer);
            
            // Fit map to show both CCTV and area kerja
            const extent = highlightedAreaKerjaLayer.getSource().getExtent();
            if (extent && extent[0] !== Infinity) {
                map.getView().fit(extent, {
                    padding: [50, 50, 50, 50],
                    duration: 500,
                    maxZoom: 17
                });
            }
        }
    }

    function showCCTVPopup(coordinate, cctv) {
        const cctvName = cctv.name || cctv.cctv_name || cctv.nama_cctv || 'CCTV';
        
        // Check if data is incomplete (missing no_cctv, site, or perusahaan)
        const hasNoCctv = (!cctv.no_cctv || cctv.no_cctv === 'N/A' || cctv.no_cctv === null) && 
                          (!cctv.nomor_cctv || cctv.nomor_cctv === 'N/A' || cctv.nomor_cctv === null);
        const hasNoSite = (!cctv.site || cctv.site === 'N/A' || cctv.site === null);
        const hasNoPerusahaan = (!cctv.perusahaan || cctv.perusahaan === 'N/A' || cctv.perusahaan === null) &&
                                 (!cctv.perusahaan_cctv || cctv.perusahaan_cctv === 'N/A' || cctv.perusahaan_cctv === null);
        
        const isDataIncomplete = hasNoCctv || hasNoSite || hasNoPerusahaan;
        
        // If data is incomplete, fetch from database
        if (isDataIncomplete && cctvName && cctvName !== 'CCTV') {
            // Show loading message
            document.getElementById('popup-content').innerHTML = `
                <div style="min-width: 250px; text-align: center; padding: 20px;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Memuat data CCTV...</p>
                </div>
            `;
            popupOverlay.setPosition(coordinate);
            
            // Fetch data from API
            fetch('{{ route("hazard-detection.api.cctv") }}?name=' + encodeURIComponent(cctvName))
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data) {
                        // Merge fetched data with existing data
                        const mergedCctv = { ...cctv, ...result.data };
                        displayCCTVPopupContent(coordinate, mergedCctv);
                    } else {
                        // If not found, display with available data
                        displayCCTVPopupContent(coordinate, cctv);
                    }
                })
                .catch(error => {
                    console.error('Error fetching CCTV data:', error);
                    // Display with available data on error
                    displayCCTVPopupContent(coordinate, cctv);
                });
        } else {
            // Data is complete, display directly
            displayCCTVPopupContent(coordinate, cctv);
        }
    }

    // Helper function untuk format tanggal dengan mengurangi 8 jam (UTC ke WIB)
    function formatDateWIB(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            // Kurangi 8 jam untuk konversi UTC ke WIB
            date.setHours(date.getHours() - 8);
            return date.toLocaleString('id-ID', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        } catch (e) {
            return 'N/A';
        }
    }

    function showUnitVehiclePopup(coordinate, unit) {
        if (!unit) {
            return;
        }

        const vehicleNumber = unit.vehicle_number || 'N/A';
        const vehicleType = unit.vehicle_type || 'Unknown';
        const vendorName = unit.vendor_name || 'N/A';
        const speed = unit.speed !== null && unit.speed !== undefined ? unit.speed + ' km/h' : 'N/A';
        const course = unit.course !== null && unit.course !== undefined ? unit.course + '°' : 'N/A';
        const battery = unit.battery !== null && unit.battery !== undefined ? unit.battery + '%' : 'N/A';

        const content = `
            <div style="min-width: 250px; background-color: #ffffff !important;">
                <div class="d-flex align-items-center gap-2 mb-2" style="background-color: #ffffff !important;">
                    <i class="material-icons-outlined text-primary">directions_car</i>
                    <h6 style="margin: 0; font-weight: 600;">${vehicleNumber}</h6>
                </div>
                <div style="border-top: 1px solid #e5e7eb; padding-top: 10px; margin-top: 10px; background-color: #ffffff !important;">
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Tipe:</strong> ${vehicleType}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Vendor:</strong> ${vendorName}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Kecepatan:</strong> ${speed}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Arah:</strong> ${course}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Baterai:</strong> ${battery}
                    </p>
                    <p style="margin: 5px 0; font-size: 12px; color: #666;">
                        <strong>Koordinat:</strong> ${unit.latitude?.toFixed(6)}, ${unit.longitude?.toFixed(6)}
                    </p>
                </div>
            </div>
        `;
        document.getElementById('popup-content').innerHTML = content;
        popupOverlay.setPosition(coordinate);
    }
    
    function showGpsOrangPopup(coordinate, user) {
        if (!user) {
            return;
        }

        const fullname = user.fullname || user.name || 'N/A';
        const sidCode = user.sid_code || user.npk || 'N/A';
        const position = user.functional_position || user.structural_position || 'N/A';
        const department = user.department_name || user.division_name || 'N/A';
        const battery = user.battery !== null && user.battery !== undefined ? user.battery + '%' : 'N/A';
        const batteryColor = user.battery < 20 ? '#8b5cf6' : user.battery < 50 ? '#f59e0b' : '#10b981';
        const latitude = user.latitude || 0;
        const longitude = user.longitude || 0;

        const content = `
            <div style="min-width: 250px; background-color: #ffffff !important;">
                <div class="d-flex align-items-center gap-2 mb-2" style="background-color: #ffffff !important;">
                    <i class="material-icons-outlined text-primary">person_pin</i>
                    <h6 style="margin: 0; font-weight: 600;">${fullname}</h6>
                </div>
                <div style="border-top: 1px solid #e5e7eb; padding-top: 10px; margin-top: 10px; background-color: #ffffff !important;">
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>SID Code:</strong> ${sidCode}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Posisi:</strong> ${position}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Departemen:</strong> ${department}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Baterai:</strong> <span style="color: ${batteryColor};">${battery}</span>
                    </p>
                    <p style="margin: 5px 0; font-size: 12px; color: #666;">
                        <strong>Koordinat:</strong> ${latitude.toFixed(6)}, ${longitude.toFixed(6)}
                    </p>
                </div>
            </div>
        `;
        document.getElementById('popup-content').innerHTML = content;
        popupOverlay.setPosition(coordinate);
    }

    function displayCCTVPopupContent(coordinate, cctv) {
        const cctvName = cctv.name || cctv.cctv_name || cctv.nama_cctv || 'CCTV';
        const cctvSite = cctv.site || 'N/A';
        const cctvStatus = cctv.status || cctv.kondisi || 'N/A';
        const linkAkses = cctv.link_akses || cctv.externalUrl || '';
        const rawRtspUrl = (cctv.rtsp_url && cctv.rtsp_url.trim() !== '') ? cctv.rtsp_url.trim() : '';
        const effectiveRtspUrl = rawRtspUrl || defaultCctvRtspUrl || '';
        const hasRtspStream = effectiveRtspUrl !== '';
        const noCctv = cctv.no_cctv || cctv.nomor_cctv || 'N/A';
        const perusahaan = cctv.perusahaan || cctv.perusahaan_cctv || 'N/A';
        
        // Highlight area kerja for this CCTV
        highlightAreaKerjaForCCTV(cctv);
        
        let actionButtons = '';
        // Tombol Stream Video
        if (hasRtspStream) {
            actionButtons += `<button type="button" class="btn btn-sm btn-primary mt-2 btn-open-stream" style="width: 100%;" 
                data-cctv-name="${cctvName.replace(/"/g, '&quot;')}" 
                data-rtsp-url="${effectiveRtspUrl.replace(/"/g, '&quot;')}">
                <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">videocam</i>
                Stream Video
            </button>`;
        } else if (linkAkses) {
            actionButtons += `<a href="${linkAkses}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mt-2" style="width: 100%;">
                <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">open_in_new</i>
                Buka Link CCTV
            </a>`;
        }
        actionButtons += `<button type="button" class="btn btn-sm btn-warning mt-2 btn-view-incidents-popup" style="width: 100%;" 
            data-cctv-name="${cctvName.replace(/"/g, '&quot;')}" 
            data-cctv-id="${(cctv.id || cctvName).toString().replace(/"/g, '&quot;')}">
            <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">report_problem</i>
            Lihat Hazard Pelaporan
        </button>`;
        actionButtons += `<button type="button" class="btn btn-sm btn-info mt-2 btn-view-pja-popup" style="width: 100%;" 
            data-cctv-name="${cctvName.replace(/"/g, '&quot;')}" 
            data-cctv-id="${(cctv.id || cctvName).toString().replace(/"/g, '&quot;')}">
            <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">construction</i>
            Lihat PJA & Laporan
        </button>`;
        actionButtons += `<button type="button" class="btn btn-sm btn-primary mt-2 btn-view-cctv-detail" style="width: 100%;" 
            data-perusahaan="${perusahaan.replace(/"/g, '&quot;')}">
            <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">list</i>
            Detail CCTV Perusahaan
        </button>`;
        
        const statusBadge = cctvStatus === 'Live View' || cctvStatus === 'live' || cctvStatus === 'Baik'
            ? '<span class="badge bg-success">' + cctvStatus + '</span>' 
            : `<span class="badge bg-secondary">${cctvStatus}</span>`;
        
        const content = `
            <div style="min-width: 250px; background-color: #ffffff !important;">
                <div class="d-flex align-items-center gap-2 mb-2" style="background-color: #ffffff !important;">
                    <i class="material-icons-outlined text-primary">videocam</i>
                    <h6 style="margin: 0; font-weight: 600;">${cctvName}</h6>
                </div>
                <div style="border-top: 1px solid #e5e7eb; padding-top: 10px; margin-top: 10px; background-color: #ffffff !important;">
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>No. CCTV:</strong> ${noCctv}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Site:</strong> ${cctvSite}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Perusahaan:</strong> ${perusahaan}
                    </p>
                    <p style="margin: 5px 0; font-size: 13px;">
                        <strong>Status:</strong> ${statusBadge}
                    </p>
                    ${cctv.lokasi_pemasangan ? `
                        <p style="margin: 5px 0; font-size: 12px; color: #666;">
                            <strong>Lokasi:</strong> ${cctv.lokasi_pemasangan}
                        </p>
                    ` : ''}
                    ${cctv.control_room ? `
                        <p style="margin: 5px 0; font-size: 12px; color: #666;">
                            <strong>Control Room:</strong> ${cctv.control_room}
                        </p>
                    ` : ''}
                    ${hasRtspStream ? `
                        <p style="margin: 5px 0; font-size: 12px; color: #666;">
                            <strong>RTSP:</strong> Tersedia
                        </p>
                    ` : ''}
                    ${actionButtons}
                </div>
            </div>
        `;
        document.getElementById('popup-content').innerHTML = content;
        popupOverlay.setPosition(coordinate);
        
        // Add event listener for view incidents button in popup
        setTimeout(function() {
            const viewIncidentsBtn = document.querySelector('.btn-view-incidents-popup');
            if (viewIncidentsBtn) {
                viewIncidentsBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const cctvName = this.getAttribute('data-cctv-name');
                    const cctvId = this.getAttribute('data-cctv-id');
                    viewCCTVIncidents(cctvName, cctvId, e);
                    popupOverlay.setPosition(undefined);
                });
            }
            
            // Add event listener for stream video button in popup
            const openStreamBtn = document.querySelector('.btn-open-stream');
            if (openStreamBtn) {
                openStreamBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const cctvName = this.getAttribute('data-cctv-name');
                    const rtspUrl = this.getAttribute('data-rtsp-url');
                    openCCTVStreamModal(cctvName, rtspUrl);
                    popupOverlay.setPosition(undefined);
                });
            }
            
            // Add event listener for view PJA button in popup
            const viewPjaBtn = document.querySelector('.btn-view-pja-popup');
            if (viewPjaBtn) {
                viewPjaBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const cctvName = this.getAttribute('data-cctv-name');
                    const cctvId = this.getAttribute('data-cctv-id');
                    viewCCTVPja(cctvName, cctvId, e);
                    popupOverlay.setPosition(undefined);
                });
            }
            
            // Add event listener for view CCTV detail button in popup
            const viewCctvDetailBtn = document.querySelector('.btn-view-cctv-detail');
            if (viewCctvDetailBtn) {
                viewCctvDetailBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const perusahaan = this.getAttribute('data-perusahaan');
                    openCctvDetailModal(perusahaan);
                    popupOverlay.setPosition(undefined);
                });
            }
        }, 100);
    }
    
    async function openCCTVStreamModal(cctvName, rtspUrl) {
        const modalTitle = document.getElementById('cctvStreamModalLabel');
        const streamFrame = document.getElementById('cctvStreamFrame');
        const streamVideo = document.getElementById('cctvStreamVideo');
        const streamLoading = document.getElementById('cctvStreamLoading');
        const modalElement = document.getElementById('cctvStreamModal');
        
        // Save current stream data for refresh functionality
        currentStreamData.cctvName = cctvName;
        currentStreamData.rtspUrl = rtspUrl || '';
        
        modalTitle.textContent = `${escapeHtml(cctvName)} - Live Stream`;
        
        // Hide all elements first
        if (streamFrame) streamFrame.style.display = 'none';
        if (streamVideo) streamVideo.style.display = 'none';
        if (streamLoading) {
            streamLoading.style.display = 'block';
            streamLoading.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Memuat stream video...</p>
            `;
        }
        
        // Reset video player if exists
        if (streamVideo) {
            resetStreamPlayer(streamVideo, streamLoading);
        }
        
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Use the CCTV stream URL
        const streamUrl = 'https://cctv-live.beraucoal.com/stream-redzone-ho/smo-101194178/102';
        
        // Set iframe source
        if (streamFrame) {
            streamFrame.src = streamUrl;
            
            // Handle iframe load
            streamFrame.onload = function() {
                if (streamLoading) {
                    streamLoading.style.display = 'none';
                }
                streamFrame.style.display = 'block';
            };
            
            // Handle iframe error
            streamFrame.onerror = function() {
                if (streamLoading) {
                    streamLoading.style.display = 'block';
                    streamLoading.innerHTML = `
                        <div class="text-center text-white">
                            <i class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error_outline</i>
                            <p class="mt-2 mb-1">Gagal memuat stream video</p>
                            <p class="small">Silakan coba refresh atau periksa koneksi internet</p>
                            <button class="btn btn-sm btn-primary mt-2" onclick="refreshCurrentStream()">
                                <i class="material-icons-outlined me-1" style="font-size: 16px;">refresh</i>
                                Coba Lagi
                            </button>
                        </div>
                    `;
                }
                streamFrame.style.display = 'none';
            };
            
            // Timeout check (if iframe doesn't load within 5 seconds)
            setTimeout(function() {
                if (streamFrame.style.display === 'none' && streamLoading && streamLoading.style.display === 'block') {
                    // Check if iframe is actually loaded (might be cross-origin issue)
                    try {
                        const frameDoc = streamFrame.contentDocument || streamFrame.contentWindow.document;
                        // If we can access, it's loaded
                        if (streamLoading) streamLoading.style.display = 'none';
                        streamFrame.style.display = 'block';
                    } catch (e) {
                        // Cross-origin is expected, assume it's loading
                        // Give it more time or show the frame anyway
                        if (streamLoading) streamLoading.style.display = 'none';
                        streamFrame.style.display = 'block';
                    }
                }
            }, 3000);
        }
        
        // Cleanup when modal is closed
        modalElement.addEventListener('hidden.bs.modal', function handleModalHide() {
            if (streamFrame) {
                streamFrame.src = '';
                streamFrame.style.display = 'none';
            }
            if (streamVideo) {
                resetStreamPlayer(streamVideo, streamLoading);
            }
            modalElement.removeEventListener('hidden.bs.modal', handleModalHide);
        });
    }
    
    // Function to refresh stream
    function refreshPythonStream(cctvName, rtspUrl) {
        const streamFrame = document.getElementById('cctvStreamFrame');
        const streamLoading = document.getElementById('cctvStreamLoading');
        
        if (!streamFrame || !streamLoading) {
            return;
        }
        
        streamLoading.style.display = 'block';
        streamFrame.style.display = 'none';
        
        // Use the CCTV stream URL with timestamp to force refresh
        const streamUrl = 'https://cctv-live.beraucoal.com/stream-redzone-ho/smo-101194178/102?t=' + Date.now();
        
        streamFrame.src = streamUrl;
        
        // Handle iframe load after refresh
        streamFrame.onload = function() {
            if (streamLoading) {
                streamLoading.style.display = 'none';
            }
            streamFrame.style.display = 'block';
        };
    }
    
    // Make refreshPythonStream globally accessible
    window.refreshPythonStream = refreshPythonStream;
    
    // Function to refresh current stream (called from refresh button in modal)
    function refreshCurrentStream() {
        if (!currentStreamData.cctvName) {
            console.warn('No stream data available to refresh');
            alert('Tidak ada data stream untuk di-refresh. Silakan tutup modal dan buka stream lagi.');
            return;
        }
        
        const streamLoading = document.getElementById('cctvStreamLoading');
        const streamFrame = document.getElementById('cctvStreamFrame');
        
        // Show loading state
        if (streamLoading) {
            streamLoading.style.display = 'block';
            streamLoading.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Memuat ulang stream video...</p>
                <small class="text-white-50 d-block">Mohon tunggu</small>
            `;
        }
        if (streamFrame) {
            streamFrame.style.display = 'none';
        }
        
        // Refresh the stream
        refreshPythonStream(currentStreamData.cctvName, currentStreamData.rtspUrl);
    }
    
    // Make refreshCurrentStream globally accessible
    window.refreshCurrentStream = refreshCurrentStream;
    
    function attachHlsStream(videoElement, playlistUrl, loadingElement) {
        if (typeof Hls !== 'undefined' && Hls.isSupported()) {
            if (currentHlsInstance) {
                currentHlsInstance.destroy();
            }
            currentHlsInstance = new Hls({
                enableWorker: true,
                lowLatencyMode: false,
                backBufferLength: 60,
            });
            currentHlsInstance.loadSource(playlistUrl);
            currentHlsInstance.attachMedia(videoElement);
            currentHlsInstance.on(Hls.Events.MANIFEST_PARSED, function() {
                loadingElement.style.display = 'none';
                videoElement.style.display = 'block';
                videoElement.play().catch(() => {});
            });
            currentHlsInstance.on(Hls.Events.ERROR, function(event, data) {
                console.warn('HLS error', data);
                if (data.fatal) {
                    currentHlsInstance.destroy();
                    currentHlsInstance = null;
                    loadingElement.style.display = 'block';
                    loadingElement.innerHTML = `
                        <div class="text-center text-white">
                            <i class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error_outline</i>
                            <p class="mt-2 mb-1">Stream terhenti</p>
                            <p class="small">Kesalahan fatal HLS: ${escapeHtml(data.type || 'Unknown')}</p>
                        </div>
                    `;
                }
            });
        } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
            videoElement.src = playlistUrl;
            videoElement.addEventListener('loadedmetadata', function handleLoaded() {
                loadingElement.style.display = 'none';
                videoElement.style.display = 'block';
                videoElement.play().catch(() => {});
                videoElement.removeEventListener('loadedmetadata', handleLoaded);
            });
            videoElement.addEventListener('error', function handleError() {
                loadingElement.style.display = 'block';
                loadingElement.innerHTML = `
                    <div class="text-center text-white">
                        <i class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error_outline</i>
                        <p class="mt-2 mb-1">Player tidak mendukung HLS</p>
                        <p class="small">Gunakan browser yang mendukung HLS native atau Hls.js.</p>
                    </div>
                `;
                videoElement.removeEventListener('error', handleError);
            });
        } else {
            loadingElement.style.display = 'block';
            loadingElement.innerHTML = `
                <div class="text-center text-white">
                    <i class="material-icons-outlined" style="font-size: 48px; color: #ef4444;">error_outline</i>
                    <p class="mt-2 mb-1">Browser tidak mendukung HLS</p>
                    <p class="small">Silakan gunakan browser modern (Chrome, Edge, Safari).</p>
                </div>
            `;
        }
    }
    
    function resetStreamPlayer(videoElement, loadingElement) {
        if (currentHlsInstance) {
            currentHlsInstance.destroy();
            currentHlsInstance = null;
        }
        videoElement.pause();
        videoElement.removeAttribute('src');
        videoElement.load();
        videoElement.style.display = 'none';
        loadingElement.style.display = 'none';
        loadingElement.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 mb-0">Menyiapkan stream HLS...</p>
            <small class="text-white-50 d-block">Pastikan koneksi RTSP dapat diakses server</small>
        `;
    }

    // Filter functionality (only if elements exist)
    const statusFilter = document.getElementById('statusFilter');
    const severityFilter = document.getElementById('severityFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterHazards);
    }
    if (severityFilter) {
        severityFilter.addEventListener('change', filterHazards);
    }
    if (typeFilter) {
        typeFilter.addEventListener('change', filterHazards);
    }

    function filterHazards() {
        const statusFilterEl = document.getElementById('statusFilter');
        const severityFilterEl = document.getElementById('severityFilter');
        const typeFilterEl = document.getElementById('typeFilter');
        
        if (!statusFilterEl || !severityFilterEl || !typeFilterEl) {
            return; // Filters not available
        }
        
        const statusFilter = statusFilterEl.value;
        const severityFilter = severityFilterEl.value;
        const typeFilter = typeFilterEl.value;

        const hazardItems = document.querySelectorAll('.hazard-item');
        hazardItems.forEach(function(item) {
            const hazardId = item.getAttribute('data-hazard-id');
            const hazard = hazardDetections.find(h => h.id === hazardId);
            
            let show = true;
            if (statusFilter !== 'all' && hazard.status !== statusFilter) show = false;
            if (severityFilter !== 'all' && hazard.severity !== severityFilter) show = false;
            if (typeFilter !== 'all' && hazard.type !== typeFilter) show = false;

            item.style.display = show ? 'block' : 'none';
        });
    }

    // Hazard item click handler
    document.querySelectorAll('.hazard-item').forEach(function(item) {
        item.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.hazard-item').forEach(i => i.classList.remove('selected'));
            
            // Add selection to clicked item
            this.classList.add('selected');
            
            // Center map on hazard
            const lat = parseFloat(this.getAttribute('data-lat'));
            const lng = parseFloat(this.getAttribute('data-lng'));
            map.getView().setCenter(ol.proj.fromLonLat([lng, lat]));
            map.getView().setZoom(16);
        });
    });

    // Function to handle photo error
    function handleHazardPhotoError(imgElement) {
        imgElement.style.display = 'none';
        const fallback = imgElement.parentElement.querySelector('.hazard-photo-fallback');
        if (fallback) {
            fallback.style.display = 'flex';
        }
    }
    
    // Function to load thumbnail photo for list view
    async function loadHazardThumbnail(container) {
        if (!container) return;
        
        const hazardId = container.getAttribute('data-hazard-id');
        const photoUrl = container.getAttribute('data-photo-url');
        const originalId = container.getAttribute('data-original-id');
        
        if (!photoUrl) {
            container.innerHTML = `
                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                    <i class="material-icons-outlined text-muted" style="font-size: 32px;">image_not_supported</i>
                </div>
            `;
            return;
        }
        
        // Extract ID from photoUrl
        const urlMatch = photoUrl.match(/\/photoCar\/(\d+)/);
        if (!urlMatch) {
            container.innerHTML = `
                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                    <i class="material-icons-outlined text-muted" style="font-size: 32px;">image</i>
                </div>
            `;
            return;
        }
        
        const photoId = urlMatch[1];
        
        try {
            // Fetch photos from API endpoint
            const apiUrl = '{{ route("hazard-detection.api.photos") }}?id=' + photoId;
            const response = await fetch(apiUrl);
            
            if (response.ok) {
                const result = await response.json();
                
                if (result.success && result.data.foto_temuan) {
                    const fotoTemuanUrl = result.data.foto_temuan;
                    container.innerHTML = `
                        <img src="${escapeHtml(fotoTemuanUrl)}" 
                             alt="Foto Temuan" 
                             class="rounded" 
                             style="width: 100%; height: 100%; object-fit: cover;"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'w-100 h-100 d-flex align-items-center justify-content-center\\'><i class=\\'material-icons-outlined text-muted\\' style=\\'font-size: 32px;\\'>broken_image</i></div>';">
                    `;
                    return;
                }
            }
        } catch (error) {
            console.warn('Error loading thumbnail:', error);
        }
        
        // Fallback: show placeholder
        container.innerHTML = `
            <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                <i class="material-icons-outlined text-muted" style="font-size: 32px;">image</i>
            </div>
        `;
    }
    
    // Load thumbnails for all hazard photos in list view
    document.addEventListener('DOMContentLoaded', function() {
        const photoContainers = document.querySelectorAll('.hazard-photo-container');
        photoContainers.forEach(function(container) {
            // Load thumbnail with slight delay to avoid blocking
            setTimeout(function() {
                loadHazardThumbnail(container);
            }, 100);
        });
    });

    // Action functions
    function viewHazardDetails(hazardId) {
        const hazard = hazardDetections.find(h => h.id === hazardId);
        if (!hazard) {
            alert('Hazard tidak ditemukan');
            return;
        }

        const modalContent = document.getElementById('hazardDetailContent');
        const modalTitle = document.getElementById('hazardDetailModalLabel');
        
        // Build title with badge
        const severityBadgeClass = hazard.severity === 'critical' ? 'bg-danger' : 
                                  hazard.severity === 'high' ? 'bg-warning' : 
                                  hazard.severity === 'medium' ? 'bg-info' : 'bg-secondary';
        const statusBadgeClass = hazard.status === 'active' ? 'bg-danger' : 'bg-success';
        const severityText = hazard.keparahan || hazard.severity || 'N/A';
        const statusText = hazard.status || 'N/A';
        
        modalTitle.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="material-icons-outlined">warning</i>
                <span>Detail Hazard</span>
                <span class="badge ${severityBadgeClass} ms-2">${escapeHtml(severityText)}</span>
                <span class="badge ${statusBadgeClass}">${escapeHtml(statusText)}</span>
            </div>
        `;
        
        // Build photo URL - gunakan url_photo yang sudah benar dari controller
        // url_photo format: https://hseautomation.beraucoal.co.id/report/photoCar/{id}
        let photoCarUrl = null;
        if (hazard.url_photo) {
            photoCarUrl = hazard.url_photo;
        } else if (hazard.original_id) {
            // Fallback jika url_photo tidak ada
            photoCarUrl = 'https://hseautomation.beraucoal.co.id/report/photoCar/' + hazard.original_id;
        }
        const hasPhoto = photoCarUrl !== null && photoCarUrl !== '';
        
        // Build content HTML with improved layout
        let contentHTML = `
            <div class="row g-4 align-items-stretch">
                <!-- Left Column - Photos (Foto Temuan & Foto Penyelesaian) -->
                <div class="col-12 col-lg-5 d-flex">
                    ${hasPhoto ? `
                        <div class="card border-0 shadow-sm w-100 d-flex flex-column">
                            <div class="card-header bg-gradient bg-primary text-white d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 d-flex align-items-center text-white">
                                    <i class="material-icons-outlined me-2">image</i> Foto Hazard
                                </h6>
                            </div>
                            <div class="card-body p-3 flex-grow-1" style="background: #f8f9fa;">
                                <div id="hazard-photos-container-${hazard.id}" class="d-flex flex-column gap-3">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 mb-0 text-muted small">Memuat foto...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ` : `
                        <div class="card border-0 shadow-sm w-100 d-flex flex-column">
                            <div class="card-body text-center py-5 flex-grow-1 d-flex align-items-center justify-content-center">
                                <div>
                                    <i class="material-icons-outlined" style="font-size: 64px; color: #6c757d;">image_not_supported</i>
                                    <p class="mt-3 text-muted mb-0">Gambar tidak tersedia</p>
                                </div>
                            </div>
                        </div>
                    `}
                </div>
                
                <!-- Right Column - Details -->
                <div class="col-12 col-lg-7 d-flex">
                    <div class="d-flex flex-column gap-3 w-100">
                        <!-- Basic Information -->
                        <div class="card border-0 shadow-sm flex-grow-1 d-flex flex-column">
                            <div class="card-header bg-gradient bg-primary text-white">
                                <h6 class="mb-0 d-flex align-items-center text-white">
                                    <i class="material-icons-outlined me-2">info</i> Informasi Dasar
                                </h6>
                            </div>
                            <div class="card-body flex-grow-1">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-primary">tag</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">ID Hazard</small>
                                                <strong class="d-block">${escapeHtml(hazard.id)}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-warning">category</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Jenis Ketidaksesuaian</small>
                                                <strong class="d-block">${escapeHtml(hazard.type || 'N/A')}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-danger">priority_high</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Keparahan</small>
                                                <span class="badge ${severityBadgeClass}">${escapeHtml(severityText)}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-success">check_circle</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Status</small>
                                                <span class="badge ${statusBadgeClass}">${escapeHtml(statusText)}</span>
                                            </div>
                                        </div>
                                    </div>
                                    ${hazard.nama_kategori ? `
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-info">label</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Kategori</small>
                                                <strong class="d-block">${escapeHtml(hazard.nama_kategori)}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                    ${hazard.nama_goldenrule ? `
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-warning">rule</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Golden Rule</small>
                                                <strong class="d-block">${escapeHtml(hazard.nama_goldenrule)}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-secondary bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-secondary">description</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Deskripsi</small>
                                                <p class="mb-0" style="line-height: 1.6;">${escapeHtml(hazard.description || 'N/A')}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       
                    </div>
                </div>
                <div class="col-12">
                     <!-- Location Information -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-gradient bg-success text-white">
                                <h6 class="mb-0 d-flex align-items-center text-white">
                                    <i class="material-icons-outlined me-2">location_on</i> Informasi Lokasi
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-1">
                                    ${hazard.site || hazard.nama_site ? `
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-success">business</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Site</small>
                                                <strong class="d-block">${escapeHtml(hazard.site || hazard.nama_site)}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                    ${hazard.nama_lokasi ? `
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-success">place</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Lokasi</small>
                                                <strong class="d-block">${escapeHtml(hazard.nama_lokasi)}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                    ${hazard.nama_detail_lokasi || hazard.lokasi_detail ? `
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-success">location_city</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Detail Lokasi</small>
                                                <strong class="d-block">${escapeHtml(hazard.nama_detail_lokasi || hazard.lokasi_detail)}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                    ${hazard.location && hazard.location.lat && hazard.location.lng ? `
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-success">map</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Koordinat</small>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-secondary">Lat: ${hazard.location.lat.toFixed(6)}</span>
                                                    <span class="badge bg-secondary">Lng: ${hazard.location.lng.toFixed(6)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reporter & PIC Information -->
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-gradient bg-info text-white">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-icons-outlined me-2">person</i> Pelapor
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-start gap-3 mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-info">account_circle</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Nama Pelapor</small>
                                                <strong class="d-block">${escapeHtml(hazard.nama_pelapor || hazard.personnel_name || 'N/A')}</strong>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-info">schedule</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Tanggal Pelaporan</small>
                                                <strong class="d-block">${escapeHtml(hazard.detected_at || hazard.tanggal_pembuatan || 'N/A')}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ${hazard.nama_pic ? `
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-gradient bg-warning text-dark">
                                        <h6 class="mb-0 d-flex align-items-center  text-white">
                                            <i class="material-icons-outlined me-2">person_pin</i> PIC
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-start gap-3 mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-warning">verified_user</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Nama PIC</small>
                                                <strong class="d-block">${escapeHtml(hazard.nama_pic)}</strong>
                                            </div>
                                        </div>
                                        ${hazard.resolved_at ? `
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-warning">done_all</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Tanggal Penyelesaian</small>
                                                <strong class="d-block">${escapeHtml(hazard.resolved_at)}</strong>
                                            </div>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                        
                        <!-- Risk Assessment & CCTV -->
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-gradient bg-danger text-white">
                                        <h6 class="mb-0 d-flex align-items-center  text-white">
                                            <i class="material-icons-outlined me-2">assessment</i> Penilaian Risiko
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-start gap-3 mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-danger">priority_high</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Keparahan</small>
                                                <strong class="d-block">${escapeHtml(hazard.keparahan || 'N/A')}</strong>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start gap-3 mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-danger">repeat</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Kekerapan</small>
                                                <strong class="d-block">${escapeHtml(hazard.kekerapan || 'N/A')}</strong>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-danger">calculate</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Nilai Risiko</small>
                                                <span class="badge ${hazard.nilai_resiko ? 'bg-primary' : 'bg-secondary'} fs-6">
                                                    ${escapeHtml(hazard.nilai_resiko || 'N/A')}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-gradient bg-secondary text-white">
                                        <h6 class="mb-0 d-flex align-items-center  text-white">
                                            <i class="material-icons-outlined me-2">videocam</i> Informasi CCTV
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-secondary bg-opacity-10 rounded-circle p-2">
                                                    <i class="material-icons-outlined text-secondary">camera</i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">CCTV ID / Tools Observation</small>
                                                <strong class="d-block">${escapeHtml(hazard.cctv_id || 'N/A')}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        `;
        
        modalContent.innerHTML = contentHTML;
        
        // Show modal using Bootstrap modal
        const modal = new bootstrap.Modal(document.getElementById('hazardDetailModal'));
        modal.show();
        
        // Load photos from photoCar page if available
        if (hasPhoto && photoCarUrl) {
            loadHazardPhotos(hazard.id, photoCarUrl);
        }
    }
    
    // Function to load photos from photoCar page using API
    async function loadHazardPhotos(hazardId, photoCarUrl) {
        const container = document.getElementById(`hazard-photos-container-${hazardId}`);
        if (!container) return;
        
        // Extract ID from photoCarUrl
        const urlMatch = photoCarUrl.match(/\/photoCar\/(\d+)/);
        if (!urlMatch) {
            container.innerHTML = `
                <div class="alert alert-warning">
                    <i class="material-icons-outlined me-2">warning</i>
                    <span>URL foto tidak valid</span>
                </div>
            `;
            return;
        }
        
        const photoId = urlMatch[1];
        
        try {
            // Fetch photos from API endpoint
            const apiUrl = '{{ route("hazard-detection.api.photos") }}?id=' + photoId;
            const response = await fetch(apiUrl);
            
            if (!response.ok) {
                throw new Error('Failed to fetch photos from API');
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Failed to extract photos');
            }
            
            const fotoTemuanUrl = result.data.foto_temuan;
            const fotoPenyelesaianUrl = result.data.foto_penyelesaian;
            
            // Build HTML for photos
            let photosHTML = '';
            
            // Foto Temuan
            if (fotoTemuanUrl) {
                photosHTML += `
                    <div class="mb-3">
                        <h6 class="mb-2 d-flex align-items-center">
                            <i class="material-icons-outlined me-2 text-danger" style="font-size: 20px;">camera_alt</i>
                            <span>Foto Temuan</span>
                        </h6>
                        <div class="border rounded p-2 bg-white" style="min-height: 200px;">
                            <img src="${escapeHtml(fotoTemuanUrl)}" 
                                 alt="Foto Temuan" 
                                 class="img-fluid w-100 rounded" 
                                 style="max-height: 400px; object-fit: contain; cursor: pointer;"
                                 onclick="window.open('${escapeHtml(fotoTemuanUrl)}', '_blank')"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'text-center py-4 text-muted\\'><i class=\\'material-icons-outlined\\'>broken_image</i><p class=\\'mt-2 mb-0 small\\'>Gagal memuat foto temuan</p></div>';">
                        </div>
                    </div>
                `;
            } else {
                photosHTML += `
                    <div class="mb-3">
                        <h6 class="mb-2 d-flex align-items-center">
                            <i class="material-icons-outlined me-2 text-danger" style="font-size: 20px;">camera_alt</i>
                            <span>Foto Temuan</span>
                        </h6>
                        <div class="border rounded p-4 bg-white text-center">
                            <i class="material-icons-outlined text-muted" style="font-size: 48px;">image_not_supported</i>
                            <p class="mt-2 mb-0 text-muted small">Foto temuan tidak tersedia</p>
                        </div>
                    </div>
                `;
            }
            
            // Foto Penyelesaian
            if (fotoPenyelesaianUrl) {
                photosHTML += `
                    <div>
                        <h6 class="mb-2 d-flex align-items-center">
                            <i class="material-icons-outlined me-2 text-success" style="font-size: 20px;">check_circle</i>
                            <span>Foto Penyelesaian</span>
                        </h6>
                        <div class="border rounded p-2 bg-white" style="min-height: 200px;">
                            <img src="${escapeHtml(fotoPenyelesaianUrl)}" 
                                 alt="Foto Penyelesaian" 
                                 class="img-fluid w-100 rounded" 
                                 style="max-height: 400px; object-fit: contain; cursor: pointer;"
                                 onclick="window.open('${escapeHtml(fotoPenyelesaianUrl)}', '_blank')"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'text-center py-4 text-muted\\'><i class=\\'material-icons-outlined\\'>broken_image</i><p class=\\'mt-2 mb-0 small\\'>Gagal memuat foto penyelesaian</p></div>';">
                        </div>
                    </div>
                `;
            } else {
                photosHTML += `
                    <div>
                        <h6 class="mb-2 d-flex align-items-center">
                            <i class="material-icons-outlined me-2 text-success" style="font-size: 20px;">check_circle</i>
                            <span>Foto Penyelesaian</span>
                        </h6>
                        <div class="border rounded p-4 bg-white text-center">
                            <i class="material-icons-outlined text-muted" style="font-size: 48px;">image_not_supported</i>
                            <p class="mt-2 mb-0 text-muted small">Foto penyelesaian belum tersedia</p>
                        </div>
                    </div>
                `;
            }
            
            container.innerHTML = photosHTML;
            
        } catch (error) {
            console.error('Error loading photos:', error);
            container.innerHTML = `
                <div class="alert alert-warning">
                    <i class="material-icons-outlined me-2">warning</i>
                    <div>
                        <p class="mb-2">Gagal memuat foto dari halaman photoCar.</p>
                        <p class="mb-2 small text-muted">Error: ${escapeHtml(error.message || 'Unknown error')}</p>
                        <p class="mb-0">Silakan buka link berikut untuk melihat foto:</p>
                        <a href="${escapeHtml(photoCarUrl)}" target="_blank" class="mt-2 d-inline-block">
                            ${escapeHtml(photoCarUrl)}
                        </a>
                    </div>
                </div>
            `;
        }
    }

    function resolveHazard(hazardId) {
        if (confirm('Are you sure you want to resolve this hazard?')) {
            // Here you would make an API call to resolve the hazard
            console.log('Resolving hazard:', hazardId);
            alert('Hazard resolved successfully!');
            // Reload page or update UI
            location.reload();
        }
    }

    function getInsidenData(noKecelakaan) {
        if (!noKecelakaan) {
            return null;
        }

        return insidenDatasetMap.get(noKecelakaan) || null;
    }

    function focusInsidenOnMap(noKecelakaan) {
        const insiden = getInsidenData(noKecelakaan);
        if (!insiden || !insiden.longitude || !insiden.latitude) {
            return;
        }

        switchView('insiden');

        const coordinate = ol.proj.fromLonLat([parseFloat(insiden.longitude), parseFloat(insiden.latitude)]);
        map.getView().animate({ center: coordinate, zoom: 16, duration: 600 });
        showInsidenPopup(coordinate, insiden);
    }

    function openInsidenModal(noKecelakaan) {
        const insiden = getInsidenData(noKecelakaan);
        if (!insiden) {
            return;
        }

        const modalTitle = document.getElementById('insidenDetailModalLabel');
        const modalContent = document.getElementById('insidenDetailContent');
        modalTitle.textContent = `Detail Insiden - ${insiden.no_kecelakaan}`;

        let rows = '';
        if (insiden.items && insiden.items.length) {
            rows = insiden.items.map(function(item, index) {
                return `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.layer || '-'}</td>
                        <td>${item.jenis_item_ipls || '-'}</td>
                        <td>${item.detail_layer || '-'}</td>
                        <td>${item.klasifikasi_layer || '-'}</td>
                        <td>${item.keterangan_layer || '-'}</td>
                    </tr>
                `;
            }).join('');
        }

        modalContent.innerHTML = `
            <div class="mb-3">
                <p class="mb-1"><strong>Site:</strong> ${insiden.site || '-'}</p>
                <p class="mb-1"><strong>Lokasi:</strong> ${insiden.lokasi || '-'}</p>
                <p class="mb-1"><strong>Status LPI:</strong> ${insiden.status_lpi || '-'}</p>
                <p class="mb-1"><strong>Kategori:</strong> ${insiden.kategori || '-'}</p>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Layer</th>
                            <th>Jenis Item IPLS</th>
                            <th>Detail Layer</th>
                            <th>Klasifikasi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows || '<tr><td colspan="6" class="text-center text-muted">Tidak ada detail tersedia</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;

        bootstrap.Modal.getOrCreateInstance(document.getElementById('insidenDetailModal')).show();
    }

    // Function to add WMS layer to map
    function addWMSLayerToMap(layerName = '', serverKey = currentWmsServer) {
        // Remove existing WMS layer if any
        if (wmsLayer) {
            map.removeLayer(wmsLayer);
            wmsLayer = null;
        }
        
        // Create and add new WMS layer
        wmsLayer = createWMSLayer(layerName, serverKey);
        map.addLayer(wmsLayer);
        currentLayer = layerName;
        
        // Update map center berdasarkan server yang dipilih
        const server = wmsServers[serverKey];
        const view = map.getView();
        view.setCenter(ol.proj.fromLonLat(server.center));
        view.setZoom(15);
        
        // Pastikan hazard dan CCTV layer selalu di atas WMS layer
        const layers = map.getLayers();
        if (hazardLayer) {
            if (layers.getArray().includes(hazardLayer)) {
                layers.remove(hazardLayer);
            }
            layers.push(hazardLayer);
        }
        if (insidenLayer) {
            if (layers.getArray().includes(insidenLayer)) {
                layers.remove(insidenLayer);
            }
            layers.push(insidenLayer);
        }
        if (cctvLayer) {
            if (layers.getArray().includes(cctvLayer)) {
                layers.remove(cctvLayer);
            }
            layers.push(cctvLayer);
        }
        if (unitVehicleLayer) {
            if (layers.getArray().includes(unitVehicleLayer)) {
                layers.remove(unitVehicleLayer);
            }
            layers.push(unitVehicleLayer);
        }
        // Ensure BMO2 PAMA layers are above WMS but below hazard and CCTV
        if (areaKerjaBmo2PamaLayer && layers.getArray().includes(areaKerjaBmo2PamaLayer)) {
            layers.remove(areaKerjaBmo2PamaLayer);
            layers.push(areaKerjaBmo2PamaLayer);
        }
        if (areaCctvBmo2PamaLayer && layers.getArray().includes(areaCctvBmo2PamaLayer)) {
            layers.remove(areaCctvBmo2PamaLayer);
            layers.push(areaCctvBmo2PamaLayer);
        }
        if (differenceBmo2PamaLayer && layers.getArray().includes(differenceBmo2PamaLayer)) {
            layers.remove(differenceBmo2PamaLayer);
            layers.push(differenceBmo2PamaLayer);
        }
        if (symmetricalDifferenceBmo2PamaLayer && layers.getArray().includes(symmetricalDifferenceBmo2PamaLayer)) {
            layers.remove(symmetricalDifferenceBmo2PamaLayer);
            layers.push(symmetricalDifferenceBmo2PamaLayer);
        }
        if (intersectionBmo2PamaLayer && layers.getArray().includes(intersectionBmo2PamaLayer)) {
            layers.remove(intersectionBmo2PamaLayer);
            layers.push(intersectionBmo2PamaLayer);
        }
    }

    // Update layer when layer select changes (only if element exists)
    const layerSelect = document.getElementById('layerSelect');
    if (layerSelect) {
        layerSelect.addEventListener('change', function(e) {
            const selectedLayer = e.target.value;
            addWMSLayerToMap(selectedLayer);
        });
    }

    // Update WMS server when server select changes (only if element exists)
    const wmsServerSelect = document.getElementById('wmsServerSelect');
    if (wmsServerSelect) {
        wmsServerSelect.addEventListener('change', function(e) {
            currentWmsServer = e.target.value;
            wmsUrl = wmsServers[currentWmsServer].url;
            
            // Reload layers dari server yang baru
            loadWMSLayers();
        });
    }

    // Update projection when projection select changes (only if element exists)
    const projectionSelect = document.getElementById('projectionSelect');
    if (projectionSelect) {
        projectionSelect.addEventListener('change', function(e) {
            const projection = e.target.value;
            const view = map.getView();
            
            const server = wmsServers[currentWmsServer];
            const newCenter = server.center;
            
            if (projection === 'EPSG:4326') {
                view.setProjection('EPSG:4326');
                view.setCenter(newCenter);
                view.setZoom(15);
            } else {
                view.setProjection('EPSG:3857');
                view.setCenter(ol.proj.fromLonLat(newCenter));
                view.setZoom(15);
            }
        });
    }

    // Try to get capabilities to list available layers
    async function loadWMSLayers(serverKey = currentWmsServer) {
        const layerSelect = document.getElementById('layerSelect');
        if (!layerSelect) {
            return; // Layer select not available
        }
        layerSelect.innerHTML = '<option value="">Loading...</option>';
        
        const server = wmsServers[serverKey];
        const serverUrl = server.url;
        
        try {
            const capabilitiesUrl = serverUrl + '?SERVICE=WMS&VERSION=1.1.1&REQUEST=GetCapabilities';
            const response = await fetch(capabilitiesUrl);
            
            if (!response.ok) {
                throw new Error('Failed to fetch capabilities');
            }
            
            const text = await response.text();
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(text, 'text/xml');
            
            // Check for parsing errors
            const parseError = xmlDoc.querySelector('parsererror');
            if (parseError) {
                throw new Error('XML parsing error');
            }
            
            // Check for service exception
            const serviceException = xmlDoc.querySelector('ServiceException');
            if (serviceException) {
                throw new Error('Service Exception: ' + serviceException.textContent);
            }
            
            // Parse layer names
            const layers = xmlDoc.querySelectorAll('Layer > Name');
            layerSelect.innerHTML = '';
            
            if (layers.length > 0) {
                layers.forEach((layer) => {
                    const layerName = layer.textContent.trim();
                    const layerElement = layer.closest('Layer');
                    const titleElement = layerElement.querySelector('Title');
                    const layerTitle = titleElement ? titleElement.textContent.trim() : layerName;
                    
                    if (layerName) {
                        const option = document.createElement('option');
                        option.value = layerName;
                        option.textContent = layerTitle || `Layer: ${layerName}`;
                        layerSelect.appendChild(option);
                    }
                });
                
                // Auto-select first layer
                if (layers.length > 0) {
                    const firstLayerName = layers[0].textContent.trim();
                    layerSelect.value = firstLayerName;
                    addWMSLayerToMap(firstLayerName, serverKey);
                }
            } else {
                // Fallback jika tidak ada layer ditemukan
                const server = wmsServers[serverKey];
                layerSelect.innerHTML = `<option value="0">${server.name} - Layer 0</option>`;
                addWMSLayerToMap('0', serverKey);
            }
        } catch (error) {
            console.warn('Could not load WMS capabilities:', error);
            console.info('Using default layer "0"');
            
            // Fallback ke layer 0
            const server = wmsServers[serverKey];
            layerSelect.innerHTML = `<option value="0">${server.name} - Layer 0</option>`;
            layerSelect.value = '0';
            addWMSLayerToMap('0', serverKey);
        }
    }

    // Load layers on page load (only if layer select exists)
    if (document.getElementById('layerSelect')) {
        loadWMSLayers();
    }

    // Handle WMS errors
    function setupErrorHandling() {
        if (wmsLayer) {
            wmsLayer.getSource().on('tileloaderror', function(event) {
                console.error('Tile load error:', event);
                console.info('Trying alternative layer names or checking server configuration might help.');
            });
        }
    }

    // Setup error handling after WMS layer is added
    map.on('rendercomplete', function() {
        setupErrorHandling();
    });

    // View Switcher Function
    function switchView(viewType) {
        const hazardListView = document.getElementById('hazardListView');
        const cctvListView = document.getElementById('cctvListView');
        const insidenListView = document.getElementById('insidenListView');
        const grListView = document.getElementById('grListView');
        const unitListView = document.getElementById('unitListView');
        const pythonAppView = document.getElementById('pythonAppView');
        const cardTitle = document.getElementById('cardTitle');
        const btnResetFilter = document.getElementById('btnResetFilter');
        const cctvStreamContainer = document.getElementById('cctvStreamContainer');

        if (hazardListView) {
            hazardListView.style.display = viewType === 'hazard' ? 'block' : 'none';
        }
        if (cctvListView) {
            cctvListView.style.display = viewType === 'cctv' ? 'block' : 'none';
        }
        if (insidenListView) {
            insidenListView.style.display = viewType === 'insiden' ? 'block' : 'none';
        }
        if (grListView) {
            grListView.style.display = viewType === 'gr' ? 'block' : 'none';
        }
        if (unitListView) {
            unitListView.style.display = viewType === 'unit' ? 'block' : 'none';
        }
        if (pythonAppView) {
            pythonAppView.style.display = viewType === 'python' ? 'block' : 'none';
        }

        if (btnResetFilter) {
            btnResetFilter.style.display = viewType === 'hazard' ? 'inline-block' : 'none';
        }


        if (viewType === 'hazard') {
            cardTitle.textContent = 'Laporan Hazard Beats';
            resetHazardFilter();
        } else if (viewType === 'cctv') {
            cardTitle.textContent = 'CCTV Stream';
            if (cctvLocations && cctvLocations.length > 0 && cctvStreamContainer && cctvStreamContainer.children.length === 0) {
                renderCCTVStreams();
            }
        } else if (viewType === 'insiden') {
            cardTitle.textContent = 'Insiden Safety';
        } else if (viewType === 'gr') {
            cardTitle.textContent = 'GR (Golden Rule)';
        } else if (viewType === 'unit') {
            cardTitle.textContent = 'Unit Kendaraan';
            
            // If unitVehicles is empty, try to refresh data first
            if (!unitVehicles || unitVehicles.length === 0) {
                console.log('Unit vehicles data is empty, refreshing...');
                if (typeof window.refreshUnitVehicles === 'function') {
                    window.refreshUnitVehicles().then(function() {
                        if (typeof window.renderUnitList === 'function') {
                            window.renderUnitList();
                        }
                    });
                } else {
                    // Wait a bit if function not yet defined
                    setTimeout(function() {
                        if (typeof window.refreshUnitVehicles === 'function') {
                            window.refreshUnitVehicles().then(function() {
                                if (typeof window.renderUnitList === 'function') {
                                    window.renderUnitList();
                                }
                            });
                        }
                    }, 100);
                }
            } else {
                // Data exists, render immediately
                if (typeof window.renderUnitList === 'function') {
                    window.renderUnitList();
                } else {
                    // Wait a bit if function not yet defined
                    setTimeout(function() {
                        if (typeof window.renderUnitList === 'function') {
                            window.renderUnitList();
                        }
                    }, 100);
                }
            }
        } else if (viewType === 'python') {
            cardTitle.textContent = 'Python Application';
            // Check if Python app is accessible
            checkPythonAppConnection();
        }

        const viewSelector = document.getElementById('viewSelector');
        if (viewSelector && viewSelector.value !== viewType) {
            viewSelector.value = viewType;
        }
    }
    
    // Function to render unit list (exposed globally)
    window.renderUnitList = function() {
        const unitListView = document.getElementById('unitListView');
        
        if (!unitListView) {
            console.warn('unitListView element not found');
            return;
        }
        
        console.log('Rendering unit list, unitVehicles count:', unitVehicles ? unitVehicles.length : 0);
        
        if (!unitVehicles || unitVehicles.length === 0) {
            console.warn('No unit vehicles data available');
            unitListView.innerHTML = '<div class="text-center text-muted py-4">Tidak ada data unit kendaraan</div>';
            return;
        }
        
        // Render unit list view
        let html = '<div class="d-flex flex-column gap-3">';
        unitVehicles.forEach(function(unit, index) {
            const unitId = unit.unit_id || unit.id || unit.integration_id || index;
            const vehicleName = unit.vehicle_name || 'N/A';
            const vehicleNumber = unit.vehicle_number || 'N/A';
            const vehicleType = unit.vehicle_type || 'Unknown';
            const vendorName = unit.vendor_name || 'N/A';
            const speed = unit.speed !== null && unit.speed !== undefined ? unit.speed + ' km/h' : 'N/A';
            const battery = unit.battery !== null && unit.battery !== undefined ? unit.battery + '%' : 'N/A';
            const updatedAt = unit.updated_at ? new Date(unit.updated_at).toLocaleString('id-ID') : 'N/A';
            const latitude = unit.latitude || 0;
            const longitude = unit.longitude || 0;
            const hasCoordinates = latitude && longitude && latitude !== 0 && longitude !== 0;
            
            // Determine color based on vehicle type
            let typeColor = 'bg-primary';
            if (vehicleType.toLowerCase().includes('dump') || vehicleType.toLowerCase().includes('truck')) {
                typeColor = 'bg-warning';
            } else if (vehicleType.toLowerCase().includes('prime') || vehicleType.toLowerCase().includes('mover')) {
                typeColor = 'bg-success';
            } else if (vehicleType.toLowerCase().includes('lube')) {
                typeColor = 'bg-info';
            }
            
            html += `
                <div class="card border-0 shadow-sm unit-item" 
                     data-unit-id="${unitId}"
                     data-latitude="${latitude}"
                     data-longitude="${longitude}"
                     ${hasCoordinates ? 'onclick="focusUnitOnMap(\'' + unitId + '\')" style="cursor: pointer; transition: transform 0.2s;"' : 'style="transition: transform 0.2s;"'}>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h6 class="mb-0">${vehicleNumber}</h6>
                                </div>
                                <div class="row g-2 small text-muted">
                                    <div class="col-6">
                                        <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">category</i>
                                        <span>Tipe: ${vehicleType}</span>
                                    </div>
                                    <div class="col-6">
                                        <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">business</i>
                                        <span>${vendorName}</span>
                                    </div>
                                    <div class="col-6">
                                        <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">speed</i>
                                        <span>${speed}</span>
                                    </div>
                                    <div class="col-6">
                                        <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">battery_charging_full</i>
                                        <span>${battery}</span>
                                    </div>
                                    <div class="col-12">
                                        <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">schedule</i>
                                        <span>Update: ${updatedAt}</span>
                                    </div>
                                    ${hasCoordinates ? `
                                    <div class="col-12">
                                        <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">location_on</i>
                                        <span>${parseFloat(latitude).toFixed(6)}, ${parseFloat(longitude).toFixed(6)}</span>
                                    </div>
                                    ` : `
                                    <div class="col-12">
                                        <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle; color: #dc3545;">location_off</i>
                                        <span class="text-danger">Tidak ada data lokasi</span>
                                    </div>
                                    `}
                                </div>
                            </div>
                            <div class="ms-2">
                                <button class="btn btn-sm ${hasCoordinates ? 'btn-outline-primary' : 'btn-outline-secondary'}" 
                                        ${hasCoordinates ? `onclick="event.stopPropagation(); focusUnitOnMap('${unitId}')"` : 'disabled title="Unit tidak memiliki data koordinat"'}
                                        style="cursor: ${hasCoordinates ? 'pointer' : 'not-allowed'};">
                                    <i class="material-icons-outlined" style="font-size: 16px;">map</i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        unitListView.innerHTML = html;
        
        // Add hover effect
        const unitItems = unitListView.querySelectorAll('.unit-item');
        unitItems.forEach(function(item) {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.15)';
            });
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            });
        });
    };
    
    // Function to focus unit on map (exposed globally)
    window.focusUnitOnMap = function(unitId) {
        if (!unitId) return;
        
        // Find unit in unitVehicles array
        const unit = unitVehicles.find(function(u) {
            const id = u.unit_id || u.id || u.integration_id;
            return id == unitId;
        });
        
        if (!unit) {
            console.warn('Unit not found:', unitId);
            alert('Unit tidak ditemukan');
            return;
        }
        
        if (!unit.latitude || !unit.longitude || unit.latitude === 0 || unit.longitude === 0) {
            console.warn('Unit does not have valid coordinates:', unitId);
            alert('Unit ini tidak memiliki data koordinat untuk ditampilkan di peta');
            return;
        }
        
        // Find feature on map
        const source = unitVehicleLayer.getSource();
        const features = source.getFeatures();
        let targetFeature = null;
        
        features.forEach(function(feature) {
            const featureUnitId = feature.get('unitId');
            if (featureUnitId == unitId) {
                targetFeature = feature;
            }
        });
        
        if (targetFeature) {
            const coordinate = targetFeature.getGeometry().getCoordinates();
            const view = map.getView();
            
            // Animate to unit location
            view.animate({
                center: coordinate,
                zoom: 17,
                duration: 600
            });
            
            // Show popup
            setTimeout(function() {
                const unitData = targetFeature.get('unitData');
                showUnitVehiclePopup(coordinate, unitData);
            }, 300);
            
            // Highlight the unit (optional: add pulse effect)
            const unitItems = document.querySelectorAll('.unit-item');
            unitItems.forEach(function(item) {
                if (item.getAttribute('data-unit-id') == unitId) {
                    item.style.border = '2px solid #3b82f6';
                    setTimeout(function() {
                        item.style.border = '';
                    }, 2000);
                }
            });
        } else {
            // If feature not found, create coordinate from unit data
            const coordinate = ol.proj.fromLonLat([parseFloat(unit.longitude), parseFloat(unit.latitude)]);
            const view = map.getView();
            
            view.animate({
                center: coordinate,
                zoom: 17,
                duration: 600
            });
            
            setTimeout(function() {
                showUnitVehiclePopup(coordinate, unit);
            }, 300);
        }
    };
    

    // Function to check Python app connection
    function checkPythonAppConnection() {
        const pythonAppFrame = document.getElementById('pythonAppFrame');
        const pythonAppLoading = document.getElementById('pythonAppLoading');
        const pythonAppError = document.getElementById('pythonAppError');
        
        if (!pythonAppFrame || !pythonAppLoading || !pythonAppError) {
            return;
        }

        // Show loading
        pythonAppLoading.style.display = 'block';
        pythonAppError.style.display = 'none';
        pythonAppFrame.style.display = 'none';

        // Try to load the iframe
        pythonAppFrame.onload = function() {
            pythonAppLoading.style.display = 'none';
            pythonAppError.style.display = 'none';
            pythonAppFrame.style.display = 'block';
        };

        pythonAppFrame.onerror = function() {
            pythonAppLoading.style.display = 'none';
            pythonAppError.style.display = 'block';
            pythonAppFrame.style.display = 'none';
        };

        // Set timeout to check if frame loads
        setTimeout(function() {
            try {
                // Try to access frame content (will fail if cross-origin)
                const frameDoc = pythonAppFrame.contentDocument || pythonAppFrame.contentWindow.document;
                pythonAppLoading.style.display = 'none';
                pythonAppError.style.display = 'none';
                pythonAppFrame.style.display = 'block';
            } catch (e) {
                // Cross-origin error is expected, frame is loading
                pythonAppLoading.style.display = 'none';
                pythonAppError.style.display = 'none';
                pythonAppFrame.style.display = 'block';
            }
        }, 2000);
    }

    // Function to refresh Python app
    // Function to view tasklist detail
    function viewTasklistDetail(tasklistId) {
        if (!tasklistId || tasklistId === 'N/A') {
            alert('Tasklist ID tidak valid');
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById('tasklistDetailModal'));
        const modalContent = document.getElementById('tasklistDetailContent');
        const modalTitle = document.getElementById('tasklistDetailModalLabel');

        // Show loading state
        modalContent.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat detail tasklist...</p>
            </div>
        `;

        modal.show();

        // Fetch tasklist detail from API
        fetch(`{{ route('hazard-detection.api.tasklist-detail') }}?tasklist_id=${encodeURIComponent(tasklistId)}`)
            .then(response => response.json())
            .then(result => {
                if (!result.success || !result.data) {
                    modalContent.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="material-icons-outlined me-2">warning</i>
                            <div>
                                <p class="mb-0">${result.message || 'Gagal memuat detail tasklist'}</p>
                            </div>
                        </div>
                    `;
                    return;
                }

                const data = result.data;
                
                // Build title with badges
                const severityBadgeClass = data.severity === 'critical' ? 'bg-danger' : 
                                          data.severity === 'high' ? 'bg-warning' : 
                                          data.severity === 'medium' ? 'bg-info' : 'bg-secondary';
                const statusBadgeClass = data.status === 'active' ? 'bg-danger' : 'bg-success';
                
                modalTitle.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <i class="material-icons-outlined">assignment</i>
                        <span>Detail Tasklist ID: ${escapeHtml(tasklistId)}</span>
                        <span class="badge ${severityBadgeClass} ms-2">${escapeHtml(data.keparahan || data.severity || 'N/A')}</span>
                        <span class="badge ${statusBadgeClass}">${escapeHtml(data.status_name || data.status || 'N/A')}</span>
                    </div>
                `;

                // Build content HTML
                let contentHTML = `
                    <div class="row g-4">
                        <!-- Left Column - Basic Information -->
                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient bg-primary text-white">
                                    <h6 class="mb-0 d-flex align-items-center text-white">
                                        <i class="material-icons-outlined me-2">info</i> Informasi Dasar
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-primary">description</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Deskripsi</label>
                                                    <p class="mb-0">${escapeHtml(data.description || 'Tidak ada deskripsi')}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-info">category</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Ketidaksesuaian</label>
                                                    <p class="mb-0">${escapeHtml(data.ketidaksesuaian || data.type || 'N/A')}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ${data.subketidaksesuaian ? `
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-secondary bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-secondary">label</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Sub Ketidaksesuaian</label>
                                                    <p class="mb-0">${escapeHtml(data.subketidaksesuaian)}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-warning">assessment</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Nilai Resiko</label>
                                                    <p class="mb-0">${escapeHtml(data.nilai_resiko || 'N/A')}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-danger">trending_up</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Keparahan</label>
                                                    <p class="mb-0">${escapeHtml(data.keparahan || 'N/A')}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-success">schedule</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Kekerapan</label>
                                                    <p class="mb-0">${escapeHtml(data.kekerapan || 'N/A')}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Location & Personnel -->
                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient bg-success text-white">
                                    <h6 class="mb-0 d-flex align-items-center text-white">
                                        <i class="material-icons-outlined me-2">location_on</i> Lokasi & Personil
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-success">place</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Site</label>
                                                    <p class="mb-0">${escapeHtml(data.site || 'N/A')}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-info">location_city</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Lokasi</label>
                                                    <p class="mb-0">${escapeHtml(data.nama_lokasi || data.zone || 'N/A')}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ${data.nama_detail_lokasi ? `
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-primary">pin_drop</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Detail Lokasi</label>
                                                    <p class="mb-0">${escapeHtml(data.nama_detail_lokasi)}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                        ${data.lokasi_detail ? `
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-secondary bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-secondary">description</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Lokasi Detail</label>
                                                    <p class="mb-0">${escapeHtml(data.lokasi_detail)}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                        ${data.nama_pelapor ? `
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-warning">person</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Pelapor</label>
                                                    <p class="mb-0">${escapeHtml(data.nama_pelapor)}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                        ${data.nama_pic ? `
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-danger">person_pin</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">PIC</label>
                                                    <p class="mb-0">${escapeHtml(data.nama_pic)}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                        ${data.cctv_id && data.cctv_id !== 'N/A' ? `
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-info">videocam</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">CCTV</label>
                                                    <p class="mb-0">${escapeHtml(data.cctv_id)}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient bg-info text-white">
                                    <h6 class="mb-0 d-flex align-items-center text-white">
                                        <i class="material-icons-outlined me-2">more_horiz</i> Informasi Tambahan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        ${data.nama_goldenrule ? `
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-warning">rule</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Golden Rule</label>
                                                    <p class="mb-0">${escapeHtml(data.nama_goldenrule)}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                        ${data.nama_kategori ? `
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-primary">category</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Kategori</label>
                                                    <p class="mb-0">${escapeHtml(data.nama_kategori)}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-success">schedule</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Tanggal Pembuatan</label>
                                                    <p class="mb-0">${escapeHtml(data.detected_at || data.tanggal_pembuatan || 'N/A')}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ${data.resolved_at ? `
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-danger">check_circle</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Tanggal Penyelesaian</label>
                                                    <p class="mb-0">${escapeHtml(data.resolved_at)}</p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                        ${data.url_photo ? `
                                        <div class="col-12">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                        <i class="material-icons-outlined text-info">image</i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label text-muted small mb-1">Foto</label>
                                                    <p class="mb-0">
                                                        <a href="${escapeHtml(data.url_photo)}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="material-icons-outlined me-1" style="font-size: 16px;">open_in_new</i>
                                                            Lihat Foto
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                modalContent.innerHTML = contentHTML;
            })
            .catch(error => {
                console.error('Error fetching tasklist detail:', error);
                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="material-icons-outlined me-2">error</i>
                        <div>
                            <p class="mb-0">Terjadi kesalahan saat memuat detail tasklist.</p>
                            <p class="mb-0 small text-muted mt-2">Error: ${escapeHtml(error.message || 'Unknown error')}</p>
                        </div>
                    </div>
                `;
            });
    }

    function refreshPythonApp() {
        const pythonAppFrame = document.getElementById('pythonAppFrame');
        const pythonAppLoading = document.getElementById('pythonAppLoading');
        const pythonAppError = document.getElementById('pythonAppError');
        
        if (!pythonAppFrame || !pythonAppLoading || !pythonAppError) {
            return;
        }

        // Show loading
        pythonAppLoading.style.display = 'block';
        pythonAppError.style.display = 'none';
        pythonAppFrame.style.display = 'none';

        // Reload iframe by appending timestamp to force refresh
        const currentSrc = pythonAppFrame.src.split('?')[0];
        pythonAppFrame.src = currentSrc + '?t=' + Date.now();

        // Check connection after reload
        setTimeout(function() {
            checkPythonAppConnection();
        }, 1000);
    }

    // Make refreshPythonApp globally accessible
    window.refreshPythonApp = refreshPythonApp;

    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Function to view CCTV incidents
    function viewCCTVIncidents(cctvName, cctvId, event) {
        if (event) {
            event.stopPropagation();
        }
        
        // Update modal title
        const modalTitle = document.getElementById('cctvIncidentsModalLabel');
        modalTitle.textContent = `Hazard Pelaporan - ${escapeHtml(cctvName)}`;
        
        const modalContent = document.getElementById('incidentsModalContent');
        
        // Show loading state
        modalContent.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat data insiden dari database...</p>
            </div>
        `;
        
        // Show modal first
        const modal = new bootstrap.Modal(document.getElementById('cctvIncidentsModal'));
        modal.show();
        
        // Fetch incidents from database via API
        const apiUrl = '{{ route("hazard-detection.api.incidents-by-cctv") }}';
        const params = new URLSearchParams({
            cctv_name: cctvName || '',
            cctv_id: cctvId || ''
        });
        
        fetch(`${apiUrl}?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    const relatedHazards = data.data;
                    
                    // Add incidents to hazardDetections array if not already present
                    relatedHazards.forEach(function(incident) {
                        const existingIndex = hazardDetections.findIndex(h => h.id === incident.id);
                        if (existingIndex === -1) {
                            hazardDetections.push(incident);
                        } else {
                            // Update existing hazard with new data
                            hazardDetections[existingIndex] = incident;
                        }
                    });
                    
                    // Build table HTML
                    let tableHTML = `
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="mb-0 fw-bold">Total Hazard Inspeksi: <span class="text-primary">${relatedHazards.length}</span></h6>
                                <p class="mb-0 text-muted small">CCTV: ${escapeHtml(cctvName)}</p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Tasklist</th>
                                        <th>Type</th>
                                        <th>Severity</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                        <th>Lokasi</th>
                                        <th>Detected At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    relatedHazards.forEach(function(hazard) {
                        const severityBadgeClass = hazard.severity === 'critical' ? 'bg-danger' : 
                                                  hazard.severity === 'high' ? 'bg-warning' : 'bg-info';
                        const statusBadgeClass = hazard.status === 'open' ? 'bg-danger' : 'bg-success';
                        
                        const description = escapeHtml(hazard.description || 'N/A');
                        const shortDescription = description.length > 100 ? description.substring(0, 100) + '...' : description;
                        
                        tableHTML += `
                            <tr>
                                <td><strong>#${escapeHtml(hazard.id)}</strong></td>
                                <td>${escapeHtml(hazard.type || 'N/A')}</td>
                                <td>
                                    <span class="badge ${severityBadgeClass}">${escapeHtml(hazard.keparahan || hazard.severity || 'N/A')}</span>
                                </td>
                                <td>
                                    <span class="badge ${statusBadgeClass}">${escapeHtml(hazard.status || 'N/A')}</span>
                                </td>
                                <td style="max-width: 300px;">
                                    <small class="text-muted" title="${description}">${shortDescription}</small>
                                </td>
                                <td>${escapeHtml(hazard.zone || 'Unknown')}</td>
                                <td><small>${escapeHtml(hazard.detected_at || hazard.tanggal_pembuatan || 'N/A')}</small></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewHazardDetails('${escapeHtml(hazard.id)}')">
                                        <i class="material-icons-outlined" style="font-size: 16px;">visibility</i>
                                        View
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    tableHTML += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    modalContent.innerHTML = tableHTML;
                } else {
                    // No incidents found
                    modalContent.innerHTML = `
                        <div class="alert alert-info text-center">
                            <i class="material-icons-outlined" style="font-size: 48px; color: #6b7280;">info</i>
                            <h6 class="mt-3">Tidak ada Hazard pelaporan ditemukan</h6>
                            <p class="mb-0 text-muted">Tidak ada Hazard yang terkait dengan CCTV: <strong>${escapeHtml(cctvName)}</strong></p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching incidents:', error);
                modalContent.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="material-icons-outlined" style="font-size: 48px; color: #dc2626;">error</i>
                        <h6 class="mt-3">Error Memuat Data</h6>
                        <p class="mb-0 text-muted">Terjadi kesalahan saat memuat data insiden. Silakan coba lagi.</p>
                    </div>
                `;
            });
    }
    
    // Function to view PJA by CCTV
    function viewCCTVPja(cctvName, cctvId, event) {
        if (event) {
            event.stopPropagation();
        }
        
        // Update modal title
        const modalTitle = document.getElementById('cctvPjaModalLabel');
        modalTitle.textContent = `PJA & Laporan - ${escapeHtml(cctvName)}`;
        
        const modalContent = document.getElementById('pjaModalContent');
        
        // Show loading state
        modalContent.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat data PJA dari database...</p>
            </div>
        `;
        
        // Show modal first
        const modal = new bootstrap.Modal(document.getElementById('cctvPjaModal'));
        modal.show();
        
        // Fetch PJA data from API
        const apiUrl = '{{ route("hazard-detection.api.pja-by-cctv") }}';
        const params = new URLSearchParams({
            cctv_name: cctvName || '',
            cctv_id: cctvId || ''
        });
        
        fetch(`${apiUrl}?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const pjaData = data.data;
                    const pjaList = pjaData.pja_list || [];
                    
                    if (pjaList.length === 0) {
                        modalContent.innerHTML = `
                            <div class="alert alert-info text-center">
                                <i class="material-icons-outlined" style="font-size: 48px; color: #6b7280;">info</i>
                                <h6 class="mt-3">Tidak ada PJA ditemukan</h6>
                                <p class="mb-0 text-muted">Tidak ada PJA yang terkait dengan lokasi CCTV: <strong>${escapeHtml(pjaData.cctv_info?.lokasi || cctvName)}</strong></p>
                            </div>
                        `;
                        return;
                    }
                    
                    // Build HTML content
                    let contentHTML = `
                        <div class="mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="material-icons-outlined me-2">videocam</i>
                                        Informasi CCTV
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Nama CCTV</small>
                                            <strong>${escapeHtml(pjaData.cctv_info?.nama_cctv || cctvName)}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">No. CCTV</small>
                                            <strong>${escapeHtml(pjaData.cctv_info?.no_cctv || 'N/A')}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Lokasi</small>
                                            <strong>${escapeHtml(pjaData.cctv_info?.lokasi || 'N/A')}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Site</small>
                                            <strong>${escapeHtml(pjaData.cctv_info?.site || 'N/A')}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h5 class="mb-0">
                                <i class="material-icons-outlined me-2">construction</i>
                                Daftar PJA (${pjaList.length})
                            </h5>
                            <p class="text-muted small mb-0">Pekerjaan Jalan Angkut di lokasi ini</p>
                        </div>
                    `;
                    
                    // Loop through each PJA
                    pjaList.forEach(function(pjaItem, index) {
                        const pja = pjaItem.pja || 'N/A';
                        const namaPjaPerson = pjaItem.nama_pja_person || 'N/A';
                        const insidenCount = pjaItem.insiden_count || 0;
                        const hazardCount = pjaItem.hazard_count || 0;
                        const insidenList = pjaItem.insiden || [];
                        const hazardList = pjaItem.hazards || [];
                        
                        contentHTML += `
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <i class="material-icons-outlined" style="font-size: 24px;">construction</i>
                                                <div>
                                                    <small class="d-block opacity-75" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Pekerjaan Jalan Angkut</small>
                                                    <h5 class="mb-0 fw-bold">${escapeHtml(pja)}</h5>
                                                    ${namaPjaPerson && namaPjaPerson !== 'N/A' ? `
                                                        <div class="d-flex align-items-center gap-1 mt-1">
                                                            <i class="material-icons-outlined" style="font-size: 16px; opacity: 0.9;">person</i>
                                                            <span style="font-size: 13px; opacity: 0.9;">${escapeHtml(namaPjaPerson)}</span>
                                                        </div>
                                                    ` : ''}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-light text-dark">
                                                <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">report_problem</i>
                                                ${insidenCount} Insiden
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">warning</i>
                                                ${hazardCount} Hazard
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-3">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="material-icons-outlined mt-1">info</i>
                                            <div class="flex-grow-1">
                                                <div class="mb-2">
                                                    <strong>Nama PJA:</strong> ${escapeHtml(pja)}
                                                </div>
                                                ${namaPjaPerson && namaPjaPerson !== 'N/A' ? `
                                                    <div class="mb-2">
                                                        <strong>Nama Orang PJA:</strong> 
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                                            <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">person</i>
                                                            ${escapeHtml(namaPjaPerson)}
                                                        </span>
                                                    </div>
                                                ` : ''}
                                                <div>
                                                    <small>Total laporan: ${insidenCount + hazardCount} (${insidenCount} Insiden, ${hazardCount} Hazard)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Insiden Section -->
                                    ${insidenCount > 0 ? `
                                        <div class="mb-4">
                                            <h6 class="mb-3 d-flex align-items-center">
                                                <i class="material-icons-outlined me-2 text-danger">report_problem</i>
                                                Insiden (${insidenCount})
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>No. Kecelakaan</th>
                                                            <th>Tanggal</th>
                                                            <th>PJA</th>
                                                            <th>Nama Orang PJA</th>
                                                            <th>Lokasi</th>
                                                            <th>Kategori</th>
                                                            <th>Status LPI</th>
                                                            <th>High Potential</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${insidenList.map(function(insiden) {
                                                            return `
                                                                <tr>
                                                                    <td><strong>${escapeHtml(insiden.no_kecelakaan || 'N/A')}</strong></td>
                                                                    <td>${escapeHtml(insiden.tanggal || 'N/A')}</td>
                                                                    <td>
                                                                        <span class="badge bg-info bg-opacity-10 text-info">
                                                                            <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">construction</i>
                                                                            ${escapeHtml(pja)}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        ${insiden.nama ? `
                                                                            <div>
                                                                                <strong>${escapeHtml(insiden.nama)}</strong>
                                                                                ${insiden.jabatan ? `<br><small class="text-muted">${escapeHtml(insiden.jabatan)}</small>` : ''}
                                                                            </div>
                                                                        ` : insiden.atasan_langsung ? `
                                                                            <div>
                                                                                <strong>${escapeHtml(insiden.atasan_langsung)}</strong>
                                                                                ${insiden.jabatan_atasan_langsung ? `<br><small class="text-muted">${escapeHtml(insiden.jabatan_atasan_langsung)}</small>` : ''}
                                                                            </div>
                                                                        ` : '<span class="text-muted">-</span>'}
                                                                    </td>
                                                                    <td>
                                                                        <small>${escapeHtml(insiden.lokasi || 'N/A')}</small>
                                                                        ${insiden.sublokasi ? `<br><small class="text-muted">${escapeHtml(insiden.sublokasi)}</small>` : ''}
                                                                    </td>
                                                                    <td>${escapeHtml(insiden.kategori || 'N/A')}</td>
                                                                    <td>
                                                                        <span class="badge ${insiden.status_lpi === 'Closed' ? 'bg-success' : 'bg-warning'}">
                                                                            ${escapeHtml(insiden.status_lpi || 'N/A')}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        ${insiden.high_potential ? 
                                                                            `<span class="badge bg-danger">${escapeHtml(insiden.high_potential)}</span>` : 
                                                                            '<span class="text-muted">-</span>'
                                                                        }
                                                                    </td>
                                                                </tr>
                                                            `;
                                                        }).join('')}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    ` : `
                                        <div class="alert alert-light text-center mb-4">
                                            <i class="material-icons-outlined text-muted">info</i>
                                            <p class="mb-0 text-muted">Tidak ada insiden untuk PJA ini</p>
                                        </div>
                                    `}
                                    
                                    <!-- Hazard Section -->
                                    ${hazardCount > 0 ? `
                                        <div>
                                            <h6 class="mb-3 d-flex align-items-center">
                                                <i class="material-icons-outlined me-2 text-warning">warning</i>
                                                Hazard (${hazardCount})
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Type</th>
                                                            <th>PJA</th>
                                                            <th>Keparahan</th>
                                                            <th>Status</th>
                                                            <th>Deskripsi</th>
                                                            <th>Tanggal</th>
                                                            <th>Pelapor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${hazardList.map(function(hazard) {
                                                            const severityBadgeClass = hazard.severity === 'critical' ? 'bg-danger' : 
                                                                                      hazard.severity === 'high' ? 'bg-warning' : 
                                                                                      hazard.severity === 'medium' ? 'bg-info' : 'bg-secondary';
                                                            const statusBadgeClass = hazard.status === 'active' ? 'bg-danger' : 'bg-success';
                                                            const description = escapeHtml(hazard.description || 'N/A');
                                                            const shortDescription = description.length > 80 ? description.substring(0, 80) + '...' : description;
                                                            
                                                            return `
                                                                <tr>
                                                                    <td><strong>${escapeHtml(hazard.id || 'N/A')}</strong></td>
                                                                    <td>${escapeHtml(hazard.type || 'N/A')}</td>
                                                                    <td>
                                                                        <span class="badge bg-info bg-opacity-10 text-info">
                                                                            <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">construction</i>
                                                                            ${escapeHtml(pja)}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge ${severityBadgeClass}">${escapeHtml(hazard.keparahan || 'N/A')}</span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge ${statusBadgeClass}">${escapeHtml(hazard.status || 'N/A')}</span>
                                                                    </td>
                                                                    <td>
                                                                        <small title="${description}">${shortDescription}</small>
                                                                    </td>
                                                                    <td><small>${escapeHtml(hazard.tanggal_pembuatan || 'N/A')}</small></td>
                                                                    <td>${escapeHtml(hazard.nama_pelapor || 'N/A')}</td>
                                                                </tr>
                                                            `;
                                                        }).join('')}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    ` : `
                                        <div class="alert alert-light text-center">
                                            <i class="material-icons-outlined text-muted">info</i>
                                            <p class="mb-0 text-muted">Tidak ada hazard untuk PJA ini</p>
                                        </div>
                                    `}
                                </div>
                            </div>
                        `;
                    });
                    
                    modalContent.innerHTML = contentHTML;
                } else {
                    modalContent.innerHTML = `
                        <div class="alert alert-warning text-center">
                            <i class="material-icons-outlined" style="font-size: 48px; color: #f59e0b;">warning</i>
                            <h6 class="mt-3">Data tidak ditemukan</h6>
                            <p class="mb-0 text-muted">${data.message || 'Tidak dapat memuat data PJA'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching PJA:', error);
                modalContent.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="material-icons-outlined" style="font-size: 48px; color: #dc2626;">error</i>
                        <h6 class="mt-3">Error Memuat Data</h6>
                        <p class="mb-0 text-muted">Terjadi kesalahan saat memuat data PJA. Silakan coba lagi.</p>
                    </div>
                `;
            });
    }
    
    // Function to reset hazard filter
    function resetHazardFilter() {
        const hazardItems = document.querySelectorAll('.hazard-item');
        hazardItems.forEach(function(item) {
            item.style.display = 'block';
        });
    }

    // Function to render CCTV streams
    function renderCCTVStreams() {
        const container = document.getElementById('cctvStreamContainer');
        if (!container || !cctvLocations || cctvLocations.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-4"><p class="mb-0">Tidak ada CCTV stream tersedia</p></div>';
            return;
        }
        
        container.innerHTML = '';
        
        cctvLocations.forEach(function(cctv, index) {
            const cctvItem = document.createElement('div');
            cctvItem.className = 'cctv-item border rounded-4 p-3';
            cctvItem.style.cursor = 'pointer';
            cctvItem.style.transition = 'all 0.2s';
            
            // Hover effect
            cctvItem.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f9fafb';
                this.style.borderColor = '#3b82f6';
            });
            cctvItem.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
                this.style.borderColor = '';
            });
            
            const rawRtspUrl = (cctv.rtsp_url && cctv.rtsp_url.trim() !== '') ? cctv.rtsp_url.trim() : '';
            const effectiveRtspUrl = rawRtspUrl || defaultCctvRtspUrl || '';
            const hasStream = effectiveRtspUrl !== '';
            const cctvName = cctv.name || cctv.cctv_name || cctv.nama_cctv || 'CCTV ' + (index + 1);
            const cctvSite = cctv.site || '';
            const cctvStatus = cctv.status || '';
            const linkAkses = cctv.link_akses || cctv.externalUrl || '';
            const perusahaan = cctv.perusahaan || cctv.perusahaan_cctv || 'N/A';
            
            // Click to open CCTV detail modal
            cctvItem.addEventListener('click', function(e) {
                // Jangan trigger jika klik pada button
                if (e.target.closest('button') || e.target.closest('a')) {
                    return;
                }
                // Buka modal detail CCTV dengan perusahaan
                openCctvDetailModal(perusahaan !== 'N/A' ? perusahaan : null);
            });
            
            cctvItem.innerHTML = `
                <div class="d-flex align-items-start gap-3">
                    <div class="position-relative" style="width: 120px; height: 80px; flex-shrink: 0;">
                        <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-dark rounded" style="background: #111;">
                            <div class="text-center text-white-50">
                                <i class="material-icons-outlined" style="font-size: 32px;">${hasStream ? 'play_circle' : 'videocam_off'}</i>
                                <p class="mb-0 small" style="font-size: 10px;">${hasStream ? 'RTSP Ready' : 'No Stream'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">${cctvName}</h6>
                        ${cctvSite ? `<p class="mb-1 text-muted small"><i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">location_on</i> ${cctvSite}</p>` : ''}
                        ${perusahaan && perusahaan !== 'N/A' ? `<p class="mb-1 text-muted small"><i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">business</i> ${perusahaan}</p>` : ''}
                        ${cctvStatus ? `
                            <span class="badge ${cctvStatus === 'Live View' ? 'bg-success' : 'bg-secondary'} mb-2">
                                ${cctvStatus}
                            </span>
                        ` : ''}
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            ${hasStream ? `
                                <button type="button" class="btn btn-sm btn-primary btn-open-stream-list" 
                                    data-cctv-name="${cctvName.replace(/"/g, '&quot;')}" 
                                    data-rtsp-url="${effectiveRtspUrl.replace(/"/g, '&quot;')}">
                                    <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">videocam</i>
                                    Stream Video
                                </button>
                            ` : linkAkses ? `
                                <a href="${linkAkses}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                    <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">open_in_new</i>
                                    Buka Link
                                </a>
                            ` : ''}
                            <button type="button" class="btn btn-sm btn-outline-info btn-view-cctv-detail-card" 
                                data-perusahaan="${perusahaan !== 'N/A' ? perusahaan.replace(/"/g, '&quot;') : ''}">
                                <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">list</i>
                                Detail CCTV
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(cctvItem);
            
            // Add event listener for view incidents button
            const viewIncidentsBtn = cctvItem.querySelector('.btn-view-incidents');
            if (viewIncidentsBtn) {
                viewIncidentsBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const cctvName = this.getAttribute('data-cctv-name');
                    const cctvId = this.getAttribute('data-cctv-id');
                    viewCCTVIncidents(cctvName, cctvId, e);
                });
            }
            
            // Add event listener for stream video button in list
            const openStreamListBtn = cctvItem.querySelector('.btn-open-stream-list');
            if (openStreamListBtn) {
                openStreamListBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const cctvName = this.getAttribute('data-cctv-name');
                    const rtspValue = this.getAttribute('data-rtsp-url');
                    openCCTVStreamModal(cctvName, rtspValue);
                });
            }
            
            // Add event listener for view CCTV detail button in card
            const viewCctvDetailCardBtn = cctvItem.querySelector('.btn-view-cctv-detail-card');
            if (viewCctvDetailCardBtn) {
                viewCctvDetailCardBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const perusahaan = this.getAttribute('data-perusahaan');
                    openCctvDetailModal(perusahaan || null);
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        switchView('hazard');
        
        // Load area kritis overview data saat halaman pertama kali dimuat
        // Pastikan filter default ke semua perusahaan & site
        currentSelectedCompany = '__all__';
        currentSelectedSite = '__all__';
        
        // Load filter options untuk halaman utama dan header
        loadMainFilterOptions();
        loadHeaderFilterOptions();
        
        loadAreaKritisOverview();
        loadChartStats();
        updateTotalCctvCount();
        
        // Event listener untuk filter di header (dropdown button)
        const headerFilterCompanyBtn = document.getElementById('headerFilterCompanyBtn');
        const headerFilterSiteBtn = document.getElementById('headerFilterSiteBtn');
        const headerFilterCompanyText = document.getElementById('headerFilterCompanyText');
        const headerFilterSiteText = document.getElementById('headerFilterSiteText');
        const btnResetHeaderFilter = document.getElementById('btnResetHeaderFilter');
        
        // Event listener untuk dropdown company di header
        const headerFilterCompanyDropdown = document.getElementById('headerFilterCompanyDropdown');
        if (headerFilterCompanyDropdown) {
            headerFilterCompanyDropdown.addEventListener('click', function(e) {
                const target = e.target.closest('.filter-option');
                if (target) {
                    e.preventDefault();
                    const value = target.getAttribute('data-value');
                    const text = target.textContent.trim();
                    currentSelectedCompany = value || '__all__';
                    
                    // Update button text di header
                    if (headerFilterCompanyText) {
                        headerFilterCompanyText.textContent = text;
                    }
                    
                    // Update button text di map filter juga
                    const mainFilterCompanyText = document.getElementById('mainFilterCompanyText');
                    if (mainFilterCompanyText) {
                        mainFilterCompanyText.textContent = text;
                    }
                    
                    // Update filter di modal juga jika ada
                    const modalFilterCompany = document.getElementById('filterCompany');
                    if (modalFilterCompany) {
                        modalFilterCompany.value = currentSelectedCompany;
                    }
                    
                    // Close dropdown
                    const dropdown = bootstrap.Dropdown.getInstance(headerFilterCompanyBtn);
                    if (dropdown) {
                        dropdown.hide();
                    }
                    
                    // Update semua statistik berdasarkan filter
                    loadChartStats();
                    loadAreaKritisOverview();
                    updateTotalCctvCount();
                }
            });
        }
        
        // Event listener untuk dropdown site di header
        const headerFilterSiteDropdown = document.getElementById('headerFilterSiteDropdown');
        if (headerFilterSiteDropdown) {
            headerFilterSiteDropdown.addEventListener('click', function(e) {
                const target = e.target.closest('.filter-option');
                if (target) {
                    e.preventDefault();
                    const value = target.getAttribute('data-value');
                    const text = target.textContent.trim();
                    currentSelectedSite = value || '__all__';
                    
                    // Update button text di header
                    if (headerFilterSiteText) {
                        headerFilterSiteText.textContent = text;
                    }
                    
                    // Update button text di map filter juga
                    const mainFilterSiteText = document.getElementById('mainFilterSiteText');
                    if (mainFilterSiteText) {
                        mainFilterSiteText.textContent = text;
                    }
                    
                    // Update filter di modal juga jika ada
                    const modalFilterSite = document.getElementById('filterSite');
                    if (modalFilterSite) {
                        modalFilterSite.value = currentSelectedSite;
                    }
                    
                    // Update currentSiteFilter untuk filter map dan hazard list
                    currentSiteFilter = (currentSelectedSite !== '__all__') ? currentSelectedSite : '';
                    filterBySite(currentSiteFilter);
                    filterHazardListView(currentSiteFilter);
                    updateStatisticsBySite(currentSiteFilter);
                    
                    // Close dropdown
                    const dropdown = bootstrap.Dropdown.getInstance(headerFilterSiteBtn);
                    if (dropdown) {
                        dropdown.hide();
                    }
                    
                    // Update semua statistik berdasarkan filter
                    loadChartStats();
                    loadAreaKritisOverview();
                    updateTotalCctvCount();
                    updateTotalCctvCount();
                }
            });
        }
        
        if (btnResetHeaderFilter) {
            btnResetHeaderFilter.addEventListener('click', function() {
                currentSelectedCompany = '__all__';
                currentSelectedSite = '__all__';
                
                // Update button text di header
                if (headerFilterCompanyText) {
                    headerFilterCompanyText.textContent = 'Semua Perusahaan';
                }
                if (headerFilterSiteText) {
                    headerFilterSiteText.textContent = 'Semua Site';
                }
                
                // Update button text di map filter juga
                const mainFilterCompanyText = document.getElementById('mainFilterCompanyText');
                const mainFilterSiteText = document.getElementById('mainFilterSiteText');
                if (mainFilterCompanyText) {
                    mainFilterCompanyText.textContent = 'Semua Perusahaan';
                }
                if (mainFilterSiteText) {
                    mainFilterSiteText.textContent = 'Semua Site';
                }
                
                // Update currentSiteFilter untuk filter map dan hazard list
                currentSiteFilter = '';
                filterBySite(currentSiteFilter);
                filterHazardListView(currentSiteFilter);
                updateStatisticsBySite(currentSiteFilter);
                
                // Update filter di modal juga
                const modalFilterCompany = document.getElementById('filterCompany');
                const modalFilterSite = document.getElementById('filterSite');
                if (modalFilterCompany) {
                    modalFilterCompany.value = '__all__';
                }
                if (modalFilterSite) {
                    modalFilterSite.value = '__all__';
                }
                
                // Update semua statistik
                loadChartStats();
                loadAreaKritisOverview();
                updateTotalCctvCount();
            });
        }
        
        // Event listener untuk filter di halaman utama (dropdown button) - di map card
        const mainFilterCompanyBtn = document.getElementById('mainFilterCompanyBtn');
        const mainFilterSiteBtn = document.getElementById('mainFilterSiteBtn');
        const mainFilterCompanyText = document.getElementById('mainFilterCompanyText');
        const mainFilterSiteText = document.getElementById('mainFilterSiteText');
        const btnResetMainFilter = document.getElementById('btnResetMainFilter');
        
        // Event listener untuk dropdown company
        const mainFilterCompanyDropdown = document.getElementById('mainFilterCompanyDropdown');
        if (mainFilterCompanyDropdown) {
            mainFilterCompanyDropdown.addEventListener('click', function(e) {
                const target = e.target.closest('.filter-option');
                if (target) {
                    e.preventDefault();
                    const value = target.getAttribute('data-value');
                    const text = target.textContent.trim();
                    currentSelectedCompany = value || '__all__';
                    
                    // Update button text di map filter
                    if (mainFilterCompanyText) {
                        mainFilterCompanyText.textContent = text;
                    }
                    
                    // Update button text di header filter juga
                    const headerFilterCompanyText = document.getElementById('headerFilterCompanyText');
                    if (headerFilterCompanyText) {
                        headerFilterCompanyText.textContent = text;
                    }
                    
                    // Update filter di modal juga jika ada
                    const modalFilterCompany = document.getElementById('filterCompany');
                    if (modalFilterCompany) {
                        modalFilterCompany.value = currentSelectedCompany;
                    }
                    
                    // Close dropdown
                    const dropdown = bootstrap.Dropdown.getInstance(mainFilterCompanyBtn);
                    if (dropdown) {
                        dropdown.hide();
                    }
                    
                    // Update semua statistik berdasarkan filter
                    loadChartStats();
                    loadAreaKritisOverview();
                    updateTotalCctvCount();
                }
            });
        }
        
        // Event listener untuk dropdown site
        const mainFilterSiteDropdown = document.getElementById('mainFilterSiteDropdown');
        if (mainFilterSiteDropdown) {
            mainFilterSiteDropdown.addEventListener('click', function(e) {
                const target = e.target.closest('.filter-option');
                if (target) {
                    e.preventDefault();
                    const value = target.getAttribute('data-value');
                    const text = target.textContent.trim();
                    currentSelectedSite = value || '__all__';
                    
                    // Update button text di map filter
                    if (mainFilterSiteText) {
                        mainFilterSiteText.textContent = text;
                    }
                    
                    // Update button text di header filter juga
                    const headerFilterSiteText = document.getElementById('headerFilterSiteText');
                    if (headerFilterSiteText) {
                        headerFilterSiteText.textContent = text;
                    }
                    
                    // Update filter di modal juga jika ada
                    const modalFilterSite = document.getElementById('filterSite');
                    if (modalFilterSite) {
                        modalFilterSite.value = currentSelectedSite;
                    }
                    
                    // Update currentSiteFilter untuk filter map dan hazard list
                    currentSiteFilter = (currentSelectedSite !== '__all__') ? currentSelectedSite : '';
                    filterBySite(currentSiteFilter);
                    filterHazardListView(currentSiteFilter);
                    updateStatisticsBySite(currentSiteFilter);
                    
                    // Close dropdown
                    const dropdown = bootstrap.Dropdown.getInstance(mainFilterSiteBtn);
                    if (dropdown) {
                        dropdown.hide();
                    }
                    
                    // Update semua statistik berdasarkan filter
                    loadChartStats();
                    loadAreaKritisOverview();
                    updateTotalCctvCount();
                }
            });
        }
        
        if (btnResetMainFilter) {
            btnResetMainFilter.addEventListener('click', function() {
                currentSelectedCompany = '__all__';
                currentSelectedSite = '__all__';
                
                // Update button text di map filter
                if (mainFilterCompanyText) {
                    mainFilterCompanyText.textContent = 'Semua Perusahaan';
                }
                if (mainFilterSiteText) {
                    mainFilterSiteText.textContent = 'Semua Site';
                }
                
                // Update button text di header filter juga
                const headerFilterCompanyText = document.getElementById('headerFilterCompanyText');
                const headerFilterSiteText = document.getElementById('headerFilterSiteText');
                if (headerFilterCompanyText) {
                    headerFilterCompanyText.textContent = 'Semua Perusahaan';
                }
                if (headerFilterSiteText) {
                    headerFilterSiteText.textContent = 'Semua Site';
                }
                
                // Update currentSiteFilter untuk filter map dan hazard list
                currentSiteFilter = '';
                filterBySite(currentSiteFilter);
                filterHazardListView(currentSiteFilter);
                updateStatisticsBySite(currentSiteFilter);
                
                // Update filter di modal juga
                const modalFilterCompany = document.getElementById('filterCompany');
                const modalFilterSite = document.getElementById('filterSite');
                if (modalFilterCompany) {
                    modalFilterCompany.value = '__all__';
                }
                if (modalFilterSite) {
                    modalFilterSite.value = '__all__';
                }
                
                // Update semua statistik
                loadChartStats();
                loadAreaKritisOverview();
                updateTotalCctvCount();
            });
        }
        
        // Add click event listener to TBC stat card
        const cctvStatCard = document.getElementById('cctvStatCard');
        if (cctvStatCard) {
            cctvStatCard.addEventListener('click', function() {
                openTbcOverviewModal();
            });
            
            // Add hover effect
            cctvStatCard.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.transform = 'scale(1.02)';
            });
            
            cctvStatCard.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
                this.style.transform = 'scale(1)';
            });
        }
    });

    // DataTable untuk modal Total CCTV
    let companyCctvTable = null;
    let currentSelectedCompany = '__all__';
    let currentSelectedSite = '__all__';
    
    // Chart instances
    let chartSiteBar = null;
    let chartStatusPie = null;
    let chartCompanyBar = null;
    let chartKondisiPie = null;
    let chartKategoriCctvPie = null;
    let chartKategoriAreaPie = null;
    let chartKategoriAktivitasPie = null;
    let chartTipeCctvBar = null;
    let chartJenisInstalasiBar = null;
    let chartTimeSeries = null;
    
    // Load awal statistik chart (termasuk mini chart Top 9 perusahaan di card YTD Insiden)
    window.addEventListener('load', function () {
        // Pastikan filter default ke semua perusahaan & site
        currentSelectedCompany = '__all__';
        currentSelectedSite = '__all__';
        // Panggil API statistik CCTV
        loadChartStats();
    });
    
    // Function untuk membuka modal TBC Overview
    function openTbcOverviewModal() {
        const modal = new bootstrap.Modal(document.getElementById('tbcOverviewModal'));
        modal.show();
        
        // Load data saat modal dibuka
        loadTbcOverviewData();
    }
    
    // Function untuk load TBC overview data
    function loadTbcOverviewData() {
        // Show loading state
        const tbcDataTableBody = document.getElementById('tbcDataTableBody');
        if (tbcDataTableBody) {
            tbcDataTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Memuat data...</td></tr>';
        }
        
        fetch(`{{ route('hazard-detection.api.tbc-overview') }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update KPI cards
                    animateNumber('tbcTotalCount', data.total_tbc || 0, 800);
                    animateNumber('tbcThisYearCount', data.statistics?.this_year || 0, 800);
                    animateNumber('tbcLastYearCount', data.statistics?.last_year || 0, 800);
                    animateNumber('tbcWithDataCount', data.statistics?.with_postgres_data || 0, 800);
                    
                    // Update severity breakdown
                    if (data.statistics?.by_severity) {
                        animateNumber('tbcSeverityCritical', data.statistics.by_severity.critical || 0, 800);
                        animateNumber('tbcSeverityHigh', data.statistics.by_severity.high || 0, 800);
                        animateNumber('tbcSeverityMedium', data.statistics.by_severity.medium || 0, 800);
                        animateNumber('tbcSeverityLow', data.statistics.by_severity.low || 0, 800);
                    }
                    
                    // Update charts
                    updateTbcCompanyChart(data.by_company || []);
                    updateTbcStatusChart(data.by_status || []);
                    
                    // Update data table
                    updateTbcDataTable(data.data || []);
                } else {
                    console.error('Error loading TBC overview:', data.message);
                    if (tbcDataTableBody) {
                        tbcDataTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Error: ' + (data.message || 'Gagal memuat data') + '</td></tr>';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading TBC overview:', error);
                if (tbcDataTableBody) {
                    tbcDataTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Error: Gagal memuat data</td></tr>';
                }
            });
    }
    
    // Chart instances untuk TBC
    let tbcCompanyChart = null;
    let tbcStatusChart = null;
    
    // Function untuk update TBC Company Chart
    function updateTbcCompanyChart(data) {
        const ctx = document.getElementById('tbcCompanyChart');
        if (!ctx) return;
        
        const labels = data.map(item => item.company || 'Tidak Diketahui');
        const values = data.map(item => item.count || 0);
        
        if (tbcCompanyChart) {
            tbcCompanyChart.destroy();
        }
        
        tbcCompanyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah TBC',
                    data: values,
                    backgroundColor: 'rgba(111, 66, 193, 0.8)',
                    borderColor: 'rgba(111, 66, 193, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    
    // Function untuk update TBC Status Chart
    function updateTbcStatusChart(data) {
        const ctx = document.getElementById('tbcStatusChart');
        if (!ctx) return;
        
        const labels = data.map(item => item.status || 'Unknown');
        const values = data.map(item => item.count || 0);
        
        const colors = [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
        ];
        
        if (tbcStatusChart) {
            tbcStatusChart.destroy();
        }
        
        tbcStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah TBC',
                    data: values,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Function untuk update TBC Data Table
    function updateTbcDataTable(data) {
        const tbcDataTableBody = document.getElementById('tbcDataTableBody');
        if (!tbcDataTableBody) return;
        
        if (data.length === 0) {
            tbcDataTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data</td></tr>';
            return;
        }
        
        let html = '';
        data.forEach((item, index) => {
            const severityBadge = {
                'critical': '<span class="badge bg-danger">Sangat Tinggi</span>',
                'high': '<span class="badge bg-warning">Tinggi</span>',
                'medium': '<span class="badge bg-info">Sedang</span>',
                'low': '<span class="badge bg-success">Rendah</span>'
            }[item.severity] || '<span class="badge bg-secondary">-</span>';
            
            const statusBadge = item.status === 'active' 
                ? '<span class="badge bg-danger">' + (item.status_name || 'Open') + '</span>'
                : '<span class="badge bg-success">' + (item.status_name || 'Closed') + '</span>';
            
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><code>${item.tasklist || '-'}</code></td>
                    <td>${(item.deskripsi || '-').substring(0, 50)}${(item.deskripsi || '').length > 50 ? '...' : ''}</td>
                    <td>${item.nama_detail_lokasi || item.lokasi_detail || '-'}</td>
                    <td>${item.nama_site || '-'}</td>
                    <td>${severityBadge}</td>
                    <td>${statusBadge}</td>
                    <td>${item.nama_pelapor || '-'}</td>
                    <td>${item.tanggal_pembuatan ? new Date(item.tanggal_pembuatan).toLocaleDateString('id-ID') : '-'}</td>
                </tr>
            `;
        });
        
        tbcDataTableBody.innerHTML = html;
    }
    
    // Function untuk membuka modal detail CCTV
    function openCctvDetailModal(perusahaan = null) {
        const modal = new bootstrap.Modal(document.getElementById('totalCctvModal'));
        
        // Set filter values if perusahaan provided, otherwise use current filter values
        if (perusahaan && perusahaan !== 'N/A') {
            currentSelectedCompany = perusahaan;
        }
        // Jika tidak ada perusahaan yang dipilih, gunakan filter yang sudah dipilih di halaman utama
        // currentSelectedCompany dan currentSelectedSite sudah memiliki nilai dari filter halaman utama
        
        modal.show();
        
        // Wait for modal to be shown, then load data
        const modalElement = document.getElementById('totalCctvModal');
        const onModalShown = function() {
            // Set filter dropdowns dengan nilai dari filter halaman utama
            const filterCompanyEl = document.getElementById('filterCompany');
            const filterSiteEl = document.getElementById('filterSite');
            
            if (filterCompanyEl) {
                filterCompanyEl.value = currentSelectedCompany || '__all__';
            }
            if (filterSiteEl) {
                filterSiteEl.value = currentSelectedSite || '__all__';
            }
            
            // Load filter options first, then load data
            loadFilterOptions();
            
            // Update label
            updateFilterLabel();
            
            // Load initial data after a short delay to ensure modal is fully rendered
            setTimeout(() => {
                loadChartStats();
                loadAreaKritisOverview();
                updateTotalCctvCount();
            }, 500);
        };
        
        // Add event listener (will be removed after first use)
        modalElement.addEventListener('shown.bs.modal', onModalShown, { once: true });
    }
    
    // Function untuk load filter options untuk modal
    function loadFilterOptions() {
        // Load companies
        fetch('{{ route("hazard-detection.api.company-overview") }}')
            .then(response => response.json())
            .then(data => {
                const companySelect = document.getElementById('filterCompany');
                if (companySelect) {
                    companySelect.innerHTML = '<option value="__all__">Semua Perusahaan</option>';
                    
                    if (data.success && data.data.length > 0) {
                        data.data.forEach(company => {
                            const option = document.createElement('option');
                            option.value = company.perusahaan;
                            option.textContent = company.perusahaan;
                            companySelect.appendChild(option);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error loading companies:', error);
            });
        
        // Load sites
        fetch('{{ route("hazard-detection.api.sites-list") }}')
            .then(response => response.json())
            .then(data => {
                const siteSelect = document.getElementById('filterSite');
                if (siteSelect) {
                    siteSelect.innerHTML = '<option value="__all__">Semua Site</option>';
                    
                    if (data.success && data.data.length > 0) {
                        data.data.forEach(site => {
                            const option = document.createElement('option');
                            option.value = site;
                            option.textContent = site;
                            siteSelect.appendChild(option);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error loading sites:', error);
            });
    }
    
    // Function untuk load filter options untuk halaman utama (dropdown button) - di map card
    function loadMainFilterOptions() {
        // Load companies
        fetch('{{ route("hazard-detection.api.company-overview") }}')
            .then(response => response.json())
            .then(data => {
                const companyDropdown = document.getElementById('mainFilterCompanyDropdown');
                if (companyDropdown) {
                    companyDropdown.innerHTML = '<li><a class="dropdown-item filter-option" href="javascript:;" data-value="__all__">Semua Perusahaan</a></li>';
                    
                    if (data.success && data.data.length > 0) {
                        data.data.forEach(company => {
                            const li = document.createElement('li');
                            const a = document.createElement('a');
                            a.className = 'dropdown-item filter-option';
                            a.href = 'javascript:;';
                            a.setAttribute('data-value', company.perusahaan);
                            a.textContent = company.perusahaan;
                            li.appendChild(a);
                            companyDropdown.appendChild(li);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error loading companies:', error);
            });
        
        // Load sites
        fetch('{{ route("hazard-detection.api.sites-list") }}')
            .then(response => response.json())
            .then(data => {
                const siteDropdown = document.getElementById('mainFilterSiteDropdown');
                if (siteDropdown) {
                    siteDropdown.innerHTML = '<li><a class="dropdown-item filter-option" href="javascript:;" data-value="__all__">Semua Site</a></li>';
                    
                    if (data.success && data.data.length > 0) {
                        data.data.forEach(site => {
                            const li = document.createElement('li');
                            const a = document.createElement('a');
                            a.className = 'dropdown-item filter-option';
                            a.href = 'javascript:;';
                            a.setAttribute('data-value', site);
                            a.textContent = site;
                            li.appendChild(a);
                            siteDropdown.appendChild(li);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error loading sites:', error);
            });
    }
    
    // Function untuk load filter options untuk header (dropdown button)
    function loadHeaderFilterOptions() {
        // Load companies
        fetch('{{ route("hazard-detection.api.company-overview") }}')
            .then(response => response.json())
            .then(data => {
                const companyDropdown = document.getElementById('headerFilterCompanyDropdown');
                if (companyDropdown) {
                    companyDropdown.innerHTML = '<li><a class="dropdown-item filter-option" href="javascript:;" data-value="__all__">Semua Perusahaan</a></li>';
                    
                    if (data.success && data.data.length > 0) {
                        data.data.forEach(company => {
                            const li = document.createElement('li');
                            const a = document.createElement('a');
                            a.className = 'dropdown-item filter-option';
                            a.href = 'javascript:;';
                            a.setAttribute('data-value', company.perusahaan);
                            a.textContent = company.perusahaan;
                            li.appendChild(a);
                            companyDropdown.appendChild(li);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error loading companies:', error);
            });
        
        // Load sites
        fetch('{{ route("hazard-detection.api.sites-list") }}')
            .then(response => response.json())
            .then(data => {
                const siteDropdown = document.getElementById('headerFilterSiteDropdown');
                if (siteDropdown) {
                    siteDropdown.innerHTML = '<li><a class="dropdown-item filter-option" href="javascript:;" data-value="__all__">Semua Site</a></li>';
                    
                    if (data.success && data.data.length > 0) {
                        data.data.forEach(site => {
                            const li = document.createElement('li');
                            const a = document.createElement('a');
                            a.className = 'dropdown-item filter-option';
                            a.href = 'javascript:;';
                            a.setAttribute('data-value', site);
                            a.textContent = site;
                            li.appendChild(a);
                            siteDropdown.appendChild(li);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error loading sites:', error);
            });
    }
    
    // Function untuk update label berdasarkan filter
    function updateFilterLabel() {
        const companyLabel = currentSelectedCompany === '__all__' ? 'Semua Perusahaan' : currentSelectedCompany;
        const siteLabel = currentSelectedSite === '__all__' ? 'Semua Site' : currentSelectedSite;
        const labelElement = document.getElementById('companyCctvCompanyLabel');
        if (labelElement) {
            labelElement.textContent = `${companyLabel} - ${siteLabel}`;
        }
    }
    
    // Function untuk load area kritis overview (untuk section di halaman utama)
    function loadAreaKritisOverview() {
        const company = currentSelectedCompany || '__all__';
        const site = currentSelectedSite || '__all__';
        
        fetch(`{{ route('hazard-detection.api.cctv-chart-stats') }}?company=${encodeURIComponent(company)}&site=${encodeURIComponent(site)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update Area Kritis Overview dengan animasi
                    animateNumber('modalJumlahAreaKritis', data.jumlahAreaKritis || 0, 800);
                    animateNumber('modalCctvAreaKritis', data.cctvAreaKritis || 0, 800);
                    animateNumber('modalCctvAreaNonKritis', data.cctvAreaNonKritis || 0, 800);
                    
                    // Update detail data di accordion body
                    const totalArea = (data.jumlahAreaKritis || 0) + (data.jumlahAreaNonKritis || 0);
                    const totalCctv = data.total || 0;
                    
                    // Update Jumlah Area Kritis detail
                    animateNumber('detailJumlahAreaKritis', data.jumlahAreaKritis || 0, 800);
                    animateNumber('detailJumlahAreaNonKritis', data.jumlahAreaNonKritis || 0, 800);
                    animateNumber('detailTotalArea', totalArea, 800);
                    
                    // Update list detail area kritis
                    updateDetailAreaKritisList(data.detailAreaKritis || []);
                    
                    // Update CCTV Area Kritis detail
                    animateNumber('detailCctvAreaKritis', data.cctvAreaKritis || 0, 800);
                    animateNumber('detailTotalCctvAreaKritis', totalCctv, 800);
                    const persentaseCctvAreaKritis = totalCctv > 0 ? ((data.cctvAreaKritis || 0) / totalCctv * 100).toFixed(1) : 0;
                    const persentaseCctvAreaKritisEl = document.getElementById('detailPersentaseCctvAreaKritis');
                    if (persentaseCctvAreaKritisEl) {
                        // Animate percentage dengan delay untuk efek yang lebih baik
                        setTimeout(() => {
                            persentaseCctvAreaKritisEl.textContent = `${persentaseCctvAreaKritis}%`;
                            persentaseCctvAreaKritisEl.style.transform = 'scale(1.1)';
                            setTimeout(() => {
                                persentaseCctvAreaKritisEl.style.transform = 'scale(1)';
                            }, 200);
                        }, 400);
                    }
                    
                    // Update CCTV Area Non Kritis detail
                    animateNumber('detailCctvAreaNonKritis', data.cctvAreaNonKritis || 0, 800);
                    animateNumber('detailTotalCctvAreaNonKritis', totalCctv, 800);
                    const persentaseCctvAreaNonKritis = totalCctv > 0 ? ((data.cctvAreaNonKritis || 0) / totalCctv * 100).toFixed(1) : 0;
                    const persentaseCctvAreaNonKritisEl = document.getElementById('detailPersentaseCctvAreaNonKritis');
                    if (persentaseCctvAreaNonKritisEl) {
                        // Animate percentage dengan delay untuk efek yang lebih baik
                        setTimeout(() => {
                            persentaseCctvAreaNonKritisEl.textContent = `${persentaseCctvAreaNonKritis}%`;
                            persentaseCctvAreaNonKritisEl.style.transform = 'scale(1.1)';
                            setTimeout(() => {
                                persentaseCctvAreaNonKritisEl.style.transform = 'scale(1)';
                            }, 200);
                        }, 400);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading area kritis overview:', error);
            });
    }
    
    // Function untuk update list detail area kritis
    function updateDetailAreaKritisList(detailAreaKritis) {
        const listContainer = document.getElementById('listDetailAreaKritis');
        if (!listContainer) return;
        
        if (!detailAreaKritis || detailAreaKritis.length === 0) {
            listContainer.innerHTML = `
                <strong>Detail Area Kritis:</strong>
                <div class="mt-2">
                    <small class="text-muted">Tidak ada data area kritis</small>
                </div>
            `;
            return;
        }
        
        let html = '<strong>Detail Area Kritis:</strong><div class="mt-2">';
        
        // Tampilkan maksimal 10 area pertama
        const maxItems = Math.min(detailAreaKritis.length, 10);
        for (let i = 0; i < maxItems; i++) {
            const area = detailAreaKritis[i];
            html += `
                <div class="d-flex align-items-center justify-content-between p-2 mb-2 bg-white border rounded">
                    <span class="text-truncate me-2" style="max-width: 60%;" title="${area.nama_area || 'N/A'}">
                        <i class="material-icons-outlined text-danger me-1" style="font-size: 16px;">location_on</i>
                        ${area.nama_area || 'N/A'}
                    </span>
                    <span class="badge bg-danger">${area.jumlah_cctv || 0} CCTV</span>
                </div>
            `;
        }
        
        if (detailAreaKritis.length > 10) {
            html += `<small class="text-muted">dan ${detailAreaKritis.length - 10} area lainnya...</small>`;
        }
        
        html += '</div>';
        listContainer.innerHTML = html;
    }
    
    // Function untuk load chart statistics
    function loadChartStats() {
        const company = currentSelectedCompany;
        const site = currentSelectedSite;
        
        fetch(`{{ route('hazard-detection.api.cctv-chart-stats') }}?company=${encodeURIComponent(company)}&site=${encodeURIComponent(site)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update KPI Summary Cards with animation
                    animateNumber('modalTotalCctv', data.total || 0, 800);
                    animateNumber('modalCctvAktif', data.cctvAktif || 0, 800);
                    animateNumber('modalCctvKondisiBaik', data.cctvKondisiBaik || 0, 800);
                    animateNumber('modalCctvAutoAlert', data.cctvAutoAlert || 0, 800);
                    animateNumber('modalCctvKondisiTidakBaik', data.cctvKondisiTidakBaik || 0, 800);

                    // Update ringkasan CCTV di kartu utama (CCTV Aktif, Kondisi Baik, Kondisi Tidak Baik)
                    const totalCctvMain = data.total || 0;

                    // CCTV Aktif
                    const cctvAktifCount = data.cctvAktif || 0;
                    const cctvAktifPercentage = totalCctvMain > 0 ? ((cctvAktifCount / totalCctvMain) * 100).toFixed(1) : 0;
                    animateNumber('statCctvAktifCount', cctvAktifCount, 800);
                    const statCctvAktifChange = document.getElementById('statCctvAktifChange');
                    const statCctvAktifText = document.getElementById('statCctvAktifText');
                    if (statCctvAktifChange) {
                        statCctvAktifChange.textContent = `${cctvAktifPercentage}%`;
                    }
                    if (statCctvAktifText) {
                        statCctvAktifText.textContent = `${cctvAktifCount.toLocaleString('id-ID')} CCTV`;
                    }
                    updateDonutChart('donutCctvAktif', cctvAktifPercentage, '#6f42c1');

                    // CCTV Kondisi Baik
                    const kondisiBaikCount = data.cctvKondisiBaik || 0;
                    const kondisiBaikPercentage = totalCctvMain > 0 ? ((kondisiBaikCount / totalCctvMain) * 100).toFixed(1) : 0;
                    animateNumber('statKondisiBaikCount', kondisiBaikCount, 800);
                    const statKondisiBaikChange = document.getElementById('statKondisiBaikChange');
                    const statKondisiBaikText = document.getElementById('statKondisiBaikText');
                    if (statKondisiBaikChange) {
                        statKondisiBaikChange.textContent = `${kondisiBaikPercentage}%`;
                    }
                    if (statKondisiBaikText) {
                        statKondisiBaikText.textContent = `${kondisiBaikCount.toLocaleString('id-ID')} CCTV`;
                    }
                    updateDonutChart('donutKondisiBaik', kondisiBaikPercentage, '#0d6efd');

                    // CCTV Kondisi Tidak Baik
                    const kondisiTidakBaikCount = data.cctvKondisiTidakBaik || 0;
                    const kondisiTidakBaikPercentage = totalCctvMain > 0 ? ((kondisiTidakBaikCount / totalCctvMain) * 100).toFixed(1) : 0;
                    animateNumber('statKondisiTidakBaikCount', kondisiTidakBaikCount, 800);
                    const statKondisiTidakBaikChange = document.getElementById('statKondisiTidakBaikChange');
                    const statKondisiTidakBaikText = document.getElementById('statKondisiTidakBaikText');
                    if (statKondisiTidakBaikChange) {
                        statKondisiTidakBaikChange.textContent = `${kondisiTidakBaikPercentage}%`;
                    }
                    if (statKondisiTidakBaikText) {
                        statKondisiTidakBaikText.textContent = `${kondisiTidakBaikCount.toLocaleString('id-ID')} CCTV`;
                    }
                    updateDonutChart('donutKondisiTidakBaik', kondisiTidakBaikPercentage, '#fd7e14');
                    
                    // Update Coverage Badge
                    const totalCctv = data.total || 0;
                    const coveragePercentage = totalCctv > 0 ? ((data.cctvAktif || 0) / totalCctv * 100).toFixed(1) : 0;
                    const coverageBadge = document.getElementById('modalCoverageBadge');
                    if (coverageBadge) {
                        coverageBadge.textContent = `${coveragePercentage}% Coverage`;
                    }

                    const criticalAreaCardCountEl = document.getElementById('criticalAreaCardCount');
                    if (criticalAreaCardCountEl) {
                        animateNumber('criticalAreaCardCount', data.cctvAreaKritis || 0, 800);
                    }
                    const criticalCoverageDescription = document.getElementById('criticalCoverageDescription');
                    const fallbackCriticalCoveragePercent = window.initialCriticalCoveragePercentage ?? 95.1;
                    // Hitung persentase coverage: (CCTV di area kritis / Total CCTV) * 100
                    const criticalCoveragePercent = totalCctv > 0 ? ((data.cctvAreaKritis || 0) / totalCctv * 100) : fallbackCriticalCoveragePercent;
                    const finalCoveragePercent = parseFloat(criticalCoveragePercent.toFixed(1));
                    window.initialCriticalCoveragePercentage = finalCoveragePercent;
                    window.chart2InitialValue = finalCoveragePercent;
                    if (criticalCoverageDescription) {
                        criticalCoverageDescription.textContent = `${finalCoveragePercent}% area kritis ter-cover CCTV`;
                    }
                    // Update chart2 dengan nilai yang sama persis dengan teks
                    const criticalCoverageChart = document.querySelector('#chart2');
                    if (criticalCoverageChart && criticalCoverageChart._apexcharts) {
                        criticalCoverageChart._apexcharts.updateSeries([finalCoveragePercent]);
                    }
                    
                    // Update Area Kritis Overview
                    animateNumber('modalJumlahAreaKritis', data.jumlahAreaKritis || 0, 800);
                    animateNumber('modalCctvAreaKritis', data.cctvAreaKritis || 0, 800);
                    animateNumber('modalCctvAreaNonKritis', data.cctvAreaNonKritis || 0, 800);
                    
                    // Update Detail Coverage Lokasi Table
                    updateDetailCoverageLokasiTable(data.detailCoverageLokasi || []);
                    
                    // Update Issues/Alert Cards
                    animateNumber('modalNotConnected', data.issues?.notConnected || 0, 800);
                    animateNumber('modalNotMirrored', data.issues?.notMirrored || 0, 800);
                    animateNumber('modalCriticalWithoutAutoAlert', data.issues?.criticalWithoutAutoAlert || 0, 800);
                    animateNumber('modalNotVerified', data.issues?.notVerified || 0, 800);
                    
                    // Update existing charts
                    updateSiteBarChart(data.distributionBySite || []);
                    updateStatusPieChart(data.statusBreakdown || []);
                    updateCompanyBarChart(data.distributionByCompany || []);
                    updateKondisiPieChart(data.kondisiBreakdown || []);
                    // Update mini chart (Top 9 perusahaan dengan CCTV terbanyak)
                    updateTopCompanyMiniChart(data.distributionByCompany || []);
                    
                    // Update new charts
                    updateKategoriCctvPieChart(data.kategoriCctvBreakdown || []);
                    updateKategoriAreaPieChart(data.kategoriAreaBreakdown || []);
                    updateKategoriAktivitasPieChart(data.kategoriAktivitasBreakdown || []);
                    updateTipeCctvBarChart(data.tipeCctvBreakdown || []);
                    updateJenisInstalasiBarChart(data.jenisInstalasiBreakdown || []);
                    updateTimeSeriesChart(data.timeSeriesData || []);
                    
                    // Update DataTable
                    if (companyCctvTable) {
                        companyCctvTable.ajax.reload();
                    }
                }
            })
            .catch(error => {
                console.error('Error loading chart stats:', error);
            });
    }
    
    // Function to update total CCTV count dynamically based on filters
    function updateTotalCctvCount() {
        const company = currentSelectedCompany || '__all__';
        const site = currentSelectedSite || '__all__';
        
        const totalCctvElement = document.getElementById('totalCctvCountDynamic');
        if (!totalCctvElement) return;
        
        // Add loading animation class
        totalCctvElement.classList.add('updating');
        
        fetch(`{{ route('hazard-detection.api.total-cctv-count') }}?company=${encodeURIComponent(company)}&site=${encodeURIComponent(site)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const targetValue = data.total || 0;
                    
                    // Animate number change
                    animateNumber('totalCctvCountDynamic', targetValue, 600);
                    
                    // Add pulse animation effect
                    totalCctvElement.classList.add('pulse-animation');
                    setTimeout(() => {
                        totalCctvElement.classList.remove('pulse-animation');
                    }, 600);
                } else {
                    console.error('Error fetching total CCTV count:', data.message);
                    totalCctvElement.textContent = '0';
                }
            })
            .catch(error => {
                console.error('Error loading total CCTV count:', error);
                totalCctvElement.textContent = '0';
            })
            .finally(() => {
                totalCctvElement.classList.remove('updating');
            });
    }
    
    // Function untuk update Site Bar Chart
    function updateSiteBarChart(data) {
        const chartElement = document.getElementById('chartSiteBar');
        if (!chartElement) return;
        
        const categories = data.map(item => item.label);
        const values = data.map(item => item.value);
        
        if (chartSiteBar) {
            chartSiteBar.updateOptions({
                series: [{
                    name: 'Jumlah CCTV',
                    data: values
                }],
                xaxis: {
                    categories: categories
                }
            });
        } else {
            chartSiteBar = new ApexCharts(chartElement, {
                series: [{
                    name: 'Jumlah CCTV',
                    data: values
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: true
                },
                xaxis: {
                    categories: categories
                },
                colors: ['#3b82f6'],
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " CCTV"
                        }
                    }
                }
            });
            chartSiteBar.render();
        }
    }
    
    // Function untuk update Status Pie Chart
    function updateStatusPieChart(data) {
        const chartElement = document.getElementById('chartStatusPie');
        if (!chartElement) return;
        
        const labels = data.map(item => item.label);
        const values = data.map(item => item.value);
        
        if (chartStatusPie) {
            chartStatusPie.updateSeries(values);
            chartStatusPie.updateOptions({
                labels: labels
            });
        } else {
            chartStatusPie = new ApexCharts(chartElement, {
                series: values,
                chart: {
                    type: 'pie',
                    height: 350
                },
                labels: labels,
                colors: ['#10b981', '#6b7280', '#f59e0b', '#ef4444', '#8b5cf6'],
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " CCTV"
                        }
                    }
                }
            });
            chartStatusPie.render();
        }
    }
    
    // Function untuk update Company Bar Chart
    function updateCompanyBarChart(data) {
        const chartElement = document.getElementById('chartCompanyBar');
        if (!chartElement) return;
        
        const categories = data.map(item => item.label);
        const values = data.map(item => item.value);
        
        if (chartCompanyBar) {
            chartCompanyBar.updateOptions({
                series: [{
                    name: 'Jumlah CCTV',
                    data: values
                }],
                xaxis: {
                    categories: categories
                }
            });
        } else {
            chartCompanyBar = new ApexCharts(chartElement, {
                series: [{
                    name: 'Jumlah CCTV',
                    data: values
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: true
                },
                xaxis: {
                    categories: categories
                },
                colors: ['#8b5cf6'],
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " CCTV"
                        }
                    }
                }
            });
            chartCompanyBar.render();
        }
    }

    // Function untuk update mini chart Top 9 Perusahaan (chart3)
    function updateTopCompanyMiniChart(data) {
        // Ambil 9 perusahaan dengan jumlah CCTV terbanyak
        const sorted = Array.isArray(data) ? [...data].sort(function(a, b) {
            const va = a && typeof a.value === 'number' ? a.value : parseFloat(a?.value) || 0;
            const vb = b && typeof b.value === 'number' ? b.value : parseFloat(b?.value) || 0;
            return vb - va;
        }) : [];

        const topCompanies = sorted.slice(0, 9);
        if (topCompanies.length === 0) {
            return;
        }

        const categories = topCompanies.map(function(item) {
            return item && item.label ? item.label : 'Perusahaan';
        });
        const values = topCompanies.map(function(item) {
            const v = item && typeof item.value === 'number' ? item.value : parseFloat(item?.value) || 0;
            return v;
        });

        // Ambil instance chart3 dari global (dibuat di public/build/js/index.js)
        const chart3 = window.chart3Instance;

        // Jika chart belum siap, coba lagi setelah sedikit delay
        if (!chart3) {
            setTimeout(function () {
                updateTopCompanyMiniChart(data);
            }, 300);
            return;
        }

        chart3.updateSeries([{
            name: 'Jumlah CCTV',
            data: values
        }]);

        chart3.updateOptions({
            xaxis: {
                categories: categories
            },
            tooltip: {
                x: {
                    show: true
                },
                y: {
                    formatter: function (val, opts) {
                        var idx = opts && typeof opts.dataPointIndex === 'number'
                            ? opts.dataPointIndex
                            : 0;
                        var company = categories[idx] || '';
                        return company + ': ' + val + ' CCTV';
                    }
                }
            }
        });
    }
    
    // Function untuk update Kondisi Pie Chart
    function updateKondisiPieChart(data) {
        const chartElement = document.getElementById('chartKondisiPie');
        if (!chartElement) return;
        
        const labels = data.map(item => item.label);
        const values = data.map(item => item.value);
        
        if (chartKondisiPie) {
            chartKondisiPie.updateSeries(values);
            chartKondisiPie.updateOptions({
                labels: labels
            });
        } else {
            chartKondisiPie = new ApexCharts(chartElement, {
                series: values,
                chart: {
                    type: 'pie',
                    height: 350
                },
                labels: labels,
                colors: ['#10b981', '#f59e0b', '#ef4444'],
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " CCTV"
                        }
                    }
                }
            });
            chartKondisiPie.render();
        }
    }

    // Function untuk update Kategori CCTV Pie Chart
    function updateKategoriCctvPieChart(data) {
        const chartElement = document.getElementById('chartKategoriCctvPie');
        if (!chartElement) return;
        
        const labels = data.map(item => item.label);
        const values = data.map(item => item.value);
        
        if (chartKategoriCctvPie) {
            chartKategoriCctvPie.updateSeries(values);
            chartKategoriCctvPie.updateOptions({ labels: labels });
        } else {
            chartKategoriCctvPie = new ApexCharts(chartElement, {
                series: values,
                chart: { type: 'pie', height: 350 },
                labels: labels,
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
                legend: { position: 'bottom' },
                tooltip: { y: { formatter: (val) => val + " CCTV" } }
            });
            chartKategoriCctvPie.render();
        }
    }

    // Function untuk update Kategori Area Pie Chart
    function updateKategoriAreaPieChart(data) {
        const chartElement = document.getElementById('chartKategoriAreaPie');
        if (!chartElement) return;
        
        // Urutkan data: Area Kritis dulu, kemudian Area Non Kritis
        const sortedData = [...data].sort((a, b) => {
            const labelA = (a.label || '').toLowerCase().trim();
            const labelB = (b.label || '').toLowerCase().trim();
            
            // Area Kritis harus muncul pertama
            if (labelA.includes('kritis') && !labelA.includes('non')) return -1;
            if (labelB.includes('kritis') && !labelB.includes('non')) return 1;
            if (labelA.includes('non') && labelA.includes('kritis')) return 1;
            if (labelB.includes('non') && labelB.includes('kritis')) return -1;
            return 0;
        });
        
        const labels = sortedData.map(item => item.label);
        const values = sortedData.map(item => item.value);
        
        // Warna khusus untuk area kritis (merah) vs non-kritis (hijau)
        // kategori_area_tercapture hanya ada 2 nilai: "Area Non Kritis" dan "Area Kritis"
        const colors = labels.map(label => {
            const lowerLabel = (label || '').toLowerCase().trim();
            // Cek spesifik untuk "Area Kritis" (tanpa "non")
            if (lowerLabel === 'area kritis' || (lowerLabel.includes('kritis') && !lowerLabel.includes('non'))) {
                return '#ef4444'; // Merah untuk Area Kritis
            } else {
                return '#10b981'; // Hijau untuk Area Non Kritis atau lainnya
            }
        });
        
        if (chartKategoriAreaPie) {
            chartKategoriAreaPie.updateSeries(values);
            chartKategoriAreaPie.updateOptions({ labels: labels, colors: colors });
        } else {
            chartKategoriAreaPie = new ApexCharts(chartElement, {
                series: values,
                chart: { type: 'pie', height: 350 },
                labels: labels,
                colors: colors,
                legend: { position: 'bottom' },
                tooltip: { y: { formatter: (val) => val + " CCTV" } }
            });
            chartKategoriAreaPie.render();
        }
    }

    // Function untuk update Kategori Aktivitas Pie Chart
    function updateKategoriAktivitasPieChart(data) {
        const chartElement = document.getElementById('chartKategoriAktivitasPie');
        if (!chartElement) return;
        
        const labels = data.map(item => item.label);
        const values = data.map(item => item.value);
        
        // Warna khusus untuk aktivitas kritis (merah/oranye) vs non-kritis (hijau/abu-abu)
        const colors = labels.map(label => {
            const lowerLabel = label.toLowerCase();
            if (lowerLabel.includes('kritis') || lowerLabel.includes('critical')) {
                return '#f59e0b'; // Oranye
            } else {
                return '#6b7280'; // Abu-abu
            }
        });
        
        if (chartKategoriAktivitasPie) {
            chartKategoriAktivitasPie.updateSeries(values);
            chartKategoriAktivitasPie.updateOptions({ labels: labels, colors: colors });
        } else {
            chartKategoriAktivitasPie = new ApexCharts(chartElement, {
                series: values,
                chart: { type: 'pie', height: 350 },
                labels: labels,
                colors: colors,
                legend: { position: 'bottom' },
                tooltip: { y: { formatter: (val) => val + " CCTV" } }
            });
            chartKategoriAktivitasPie.render();
        }
    }

    // Function untuk update Tipe CCTV Bar Chart
    function updateTipeCctvBarChart(data) {
        const chartElement = document.getElementById('chartTipeCctvBar');
        if (!chartElement) return;
        
        const categories = data.map(item => item.label);
        const values = data.map(item => item.value);
        
        if (chartTipeCctvBar) {
            chartTipeCctvBar.updateOptions({
                series: [{ name: 'Jumlah CCTV', data: values }],
                xaxis: { categories: categories }
            });
        } else {
            chartTipeCctvBar = new ApexCharts(chartElement, {
                series: [{ name: 'Jumlah CCTV', data: values }],
                chart: { type: 'bar', height: 350, toolbar: { show: true } },
                plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                dataLabels: { enabled: true },
                xaxis: { categories: categories },
                colors: ['#06b6d4'],
                tooltip: { y: { formatter: (val) => val + " CCTV" } }
            });
            chartTipeCctvBar.render();
        }
    }

    // Function untuk update Jenis Instalasi Bar Chart
    function updateJenisInstalasiBarChart(data) {
        const chartElement = document.getElementById('chartJenisInstalasiBar');
        if (!chartElement) return;
        
        const categories = data.map(item => item.label);
        const values = data.map(item => item.value);
        
        if (chartJenisInstalasiBar) {
            chartJenisInstalasiBar.updateOptions({
                series: [{ name: 'Jumlah CCTV', data: values }],
                xaxis: { categories: categories }
            });
        } else {
            chartJenisInstalasiBar = new ApexCharts(chartElement, {
                series: [{ name: 'Jumlah CCTV', data: values }],
                chart: { type: 'bar', height: 350, toolbar: { show: true } },
                plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                dataLabels: { enabled: true },
                xaxis: { categories: categories },
                colors: ['#8b5cf6'],
                tooltip: { y: { formatter: (val) => val + " CCTV" } }
            });
            chartJenisInstalasiBar.render();
        }
    }

    // Function untuk update Time Series Chart
    function updateTimeSeriesChart(data) {
        const chartElement = document.getElementById('chartTimeSeries');
        if (!chartElement) return;
        
        const categories = data.map(item => item.label);
        const values = data.map(item => item.value);
        
        if (chartTimeSeries) {
            chartTimeSeries.updateOptions({
                series: [{ name: 'Jumlah CCTV', data: values }],
                xaxis: { categories: categories }
            });
        } else {
            chartTimeSeries = new ApexCharts(chartElement, {
                series: [{ name: 'Jumlah CCTV', data: values }],
                chart: { type: 'area', height: 350, toolbar: { show: true }, zoom: { enabled: true } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.3 } },
                xaxis: { categories: categories },
                colors: ['#3b82f6'],
                tooltip: { y: { formatter: (val) => val + " CCTV" } }
            });
            chartTimeSeries.render();
        }
    }

    // Function untuk update Detail Coverage Lokasi Table
    function updateDetailCoverageLokasiTable(data) {
        const tbody = document.getElementById('detailCoverageLokasiTableBody');
        if (!tbody) return;
        
        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        Tidak ada data coverage lokasi.
                    </td>
                </tr>
            `;
            return;
        }
        
        // Urutkan: kritis dulu, kemudian non kritis
        const sortedData = [...data].sort((a, b) => {
            if (a.is_kritis && !b.is_kritis) return -1;
            if (!a.is_kritis && b.is_kritis) return 1;
            return b.jumlah_cctv - a.jumlah_cctv;
        });
        
        let rowsHtml = '';
        sortedData.forEach((lokasi, index) => {
            const statusBadge = lokasi.is_kritis 
                ? '<span class="badge bg-danger px-3 py-2">Area Kritis</span>'
                : '<span class="badge bg-success px-3 py-2">Area Non Kritis</span>';
            
            const jumlahBadge = lokasi.is_kritis
                ? `<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">${lokasi.jumlah_cctv.toLocaleString('id-ID')} CCTV</span>`
                : `<span class="badge bg-success bg-opacity-10 text-success px-3 py-2">${lokasi.jumlah_cctv.toLocaleString('id-ID')} CCTV</span>`;
            
            rowsHtml += `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <span class="fw-semibold">${escapeHtml(lokasi.nama_lokasi || 'Tidak Diketahui')}</span>
                    </td>
                    <td class="text-end">
                        ${jumlahBadge}
                    </td>
                    <td class="text-center">
                        ${statusBadge}
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = rowsHtml;
    }

    // Inisialisasi DataTable saat modal dibuka
    const totalCctvModal = document.getElementById('totalCctvModal');
    if (totalCctvModal) {
        totalCctvModal.addEventListener('shown.bs.modal', function () {
            if (!companyCctvTable) {
                companyCctvTable = $('#companyCctvTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('hazard-detection.api.company-cctv-data') }}",
                        type: "GET",
                        data: function (d) {
                            d.company = currentSelectedCompany;
                            d.site = currentSelectedSite;
                        },
                        dataFilter: function(data) {
                            var json = jQuery.parseJSON(data);
                            document.getElementById('companyCctvCount').textContent = `${json.recordsFiltered} CCTV`;
                            return JSON.stringify(json);
                        },
                        error: function(xhr, error, thrown) {
                            console.error("DataTables AJAX error:", thrown, xhr);
                            document.getElementById('companyCctvCount').textContent = '0 CCTV';
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
                        { data: 'site', name: 'site', width: '100px' },
                        { data: 'perusahaan', name: 'perusahaan', width: '150px' },
                        { data: 'no_cctv', name: 'no_cctv', className: 'fw-semibold text-primary', width: '120px' },
                        { data: 'nama_cctv', name: 'nama_cctv', width: '150px' },
                        { data: 'status', name: 'status', orderable: false, searchable: false, width: '100px' },
                        { data: 'kondisi', name: 'kondisi', orderable: false, searchable: false, width: '100px' },
                        { data: 'coverage_lokasi', name: 'coverage_lokasi', width: '150px' },
                        { data: 'coverage_detail_lokasi', name: 'coverage_detail_lokasi', width: '150px' },
                        { data: 'kategori_area_tercapture', name: 'kategori_area_tercapture', width: '150px' },
                        { data: 'lokasi_pemasangan', name: 'lokasi_pemasangan', width: '150px' }
                    ],
                    order: [[3, 'asc']],
                    pageLength: 25,
                    scrollX: true,
                    scrollY: '400px',
                    scrollCollapse: true,
                    autoWidth: false,
                    language: {
                        processing: "Memproses data...",
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        },
                        emptyTable: "Klik perusahaan di tabel sebelah kiri untuk menampilkan daftar CCTV.",
                        zeroRecords: "Tidak ada data yang cocok dengan pencarian"
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                });
            }
        });

        // Handler untuk filter otomatis saat dropdown berubah di modal
        // Menggunakan event delegation untuk memastikan event listener selalu bekerja
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'filterCompany') {
                currentSelectedCompany = e.target.value || '__all__';
                updateFilterLabel();
                // Update filter di halaman utama (dropdown button di map card)
                const mainFilterCompanyText = document.getElementById('mainFilterCompanyText');
                if (mainFilterCompanyText) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    mainFilterCompanyText.textContent = selectedOption ? selectedOption.textContent : 'Semua Perusahaan';
                }
                // Update filter di header juga
                const headerFilterCompanyText = document.getElementById('headerFilterCompanyText');
                if (headerFilterCompanyText) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    headerFilterCompanyText.textContent = selectedOption ? selectedOption.textContent : 'Semua Perusahaan';
                }
                // Update semua statistik berdasarkan filter yang dipilih
                loadChartStats();
                loadAreaKritisOverview();
                updateTotalCctvCount();
            }
            
            if (e.target && e.target.id === 'filterSite') {
                currentSelectedSite = e.target.value || '__all__';
                updateFilterLabel();
                // Update filter di halaman utama (dropdown button di map card)
                const mainFilterSiteText = document.getElementById('mainFilterSiteText');
                if (mainFilterSiteText) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    mainFilterSiteText.textContent = selectedOption ? selectedOption.textContent : 'Semua Site';
                }
                // Update filter di header juga
                const headerFilterSiteText = document.getElementById('headerFilterSiteText');
                if (headerFilterSiteText) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    headerFilterSiteText.textContent = selectedOption ? selectedOption.textContent : 'Semua Site';
                }
                // Update currentSiteFilter untuk filter map dan hazard list
                currentSiteFilter = (currentSelectedSite !== '__all__') ? currentSelectedSite : '';
                filterBySite(currentSiteFilter);
                filterHazardListView(currentSiteFilter);
                updateStatisticsBySite(currentSiteFilter);
                // Update semua statistik berdasarkan filter yang dipilih
                loadChartStats();
                loadAreaKritisOverview();
                updateTotalCctvCount();
            }
        });
        
        // Handler untuk reset filter di modal
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'btnResetFilter') {
                currentSelectedCompany = '__all__';
                currentSelectedSite = '__all__';
                
                const filterCompany = document.getElementById('filterCompany');
                const filterSite = document.getElementById('filterSite');
                
                if (filterCompany) {
                    filterCompany.value = '__all__';
                }
                if (filterSite) {
                    filterSite.value = '__all__';
                }
                
                // Update filter di halaman utama (dropdown button di map card)
                const mainFilterCompanyText = document.getElementById('mainFilterCompanyText');
                const mainFilterSiteText = document.getElementById('mainFilterSiteText');
                if (mainFilterCompanyText) {
                    mainFilterCompanyText.textContent = 'Semua Perusahaan';
                }
                if (mainFilterSiteText) {
                    mainFilterSiteText.textContent = 'Semua Site';
                }
                // Update filter di header juga
                const headerFilterCompanyText = document.getElementById('headerFilterCompanyText');
                const headerFilterSiteText = document.getElementById('headerFilterSiteText');
                if (headerFilterCompanyText) {
                    headerFilterCompanyText.textContent = 'Semua Perusahaan';
                }
                if (headerFilterSiteText) {
                    headerFilterSiteText.textContent = 'Semua Site';
                }
                
                updateFilterLabel();
                
                // Load chart stats
                loadChartStats();
                // Update area kritis overview saat filter di-reset
                loadAreaKritisOverview();
            }
        });

        // Reset saat modal ditutup
        totalCctvModal.addEventListener('hidden.bs.modal', function () {
            currentSelectedCompany = '__all__';
            document.getElementById('companyCctvCompanyLabel').textContent = 'Pilih perusahaan untuk melihat rincian';
            document.getElementById('companyCctvCount').textContent = '0 CCTV';
            document.querySelectorAll('.company-row-trigger').forEach(r => r.classList.remove('table-active'));
            
            // Reset statistik
            document.getElementById('companyStatsAktif').textContent = '0';
            document.getElementById('companyStatsNonAktif').textContent = '0';
            document.getElementById('companyStatsAreaKritis').textContent = '0';
            
            if (companyCctvTable) {
                companyCctvTable.clear().draw();
            }
        });
    }

    // Function to toggle layer visibility
    function toggleLayerVisibility(layerType, show) {
        layerVisibility[layerType] = show;
        
        let targetLayer = null;
        switch(layerType) {
            case 'cctv':
                targetLayer = cctvLayer;
                break;
            case 'hazard':
                targetLayer = hazardLayer;
                break;
            case 'gr':
                targetLayer = grLayer;
                break;
            case 'insiden':
                targetLayer = insidenLayer;
                break;
            case 'unit':
                targetLayer = unitVehicleLayer;
                break;
            case 'gps':
                // GPS layer removed - no longer used
                targetLayer = null;
                break;
        }
        
        if (targetLayer) {
            targetLayer.setVisible(show);
        }
        
        // Update button state
        const toggleBtn = document.getElementById('toggle' + layerType.charAt(0).toUpperCase() + layerType.slice(1));
        if (toggleBtn) {
            if (show) {
                toggleBtn.classList.add('active');
            } else {
                toggleBtn.classList.remove('active');
            }
        }
        
        // Update category item state for CCTV
        if (layerType === 'cctv') {
            const cctvCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
                const span = item.querySelector('span');
                return span && span.textContent.trim() === 'CCTV';
            });
            if (cctvCategoryItem) {
                if (show) {
                    cctvCategoryItem.classList.add('active');
                } else {
                    cctvCategoryItem.classList.remove('active');
                }
            }
        }
        
        // Update category item state for Insiden
        if (layerType === 'insiden') {
            const insidenCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
                const span = item.querySelector('span');
                return span && span.textContent.trim() === 'Insiden';
            });
            if (insidenCategoryItem) {
                if (show) {
                    insidenCategoryItem.classList.add('active');
                } else {
                    insidenCategoryItem.classList.remove('active');
                }
            }
        }
    }
    
    // Event listeners for layer toggle buttons
    document.addEventListener('DOMContentLoaded', function() {
        // CCTV toggle
        const toggleCctvBtn = document.getElementById('toggleCctv');
        if (toggleCctvBtn) {
            toggleCctvBtn.addEventListener('click', function() {
                const isActive = this.classList.contains('active');
                toggleLayerVisibility('cctv', !isActive);
            });
        }
        
        // Hazard toggle
        const toggleHazardBtn = document.getElementById('toggleHazard');
        if (toggleHazardBtn) {
            toggleHazardBtn.addEventListener('click', function() {
                const isActive = this.classList.contains('active');
                toggleLayerVisibility('hazard', !isActive);
            });
        }
        
        // GR toggle
        const toggleGrBtn = document.getElementById('toggleGr');
        if (toggleGrBtn) {
            toggleGrBtn.addEventListener('click', function() {
                const isActive = this.classList.contains('active');
                toggleLayerVisibility('gr', !isActive);
            });
        }
        
        // Insiden toggle
        const toggleInsidenBtn = document.getElementById('toggleInsiden');
        if (toggleInsidenBtn) {
            toggleInsidenBtn.addEventListener('click', function() {
                const isActive = this.classList.contains('active');
                toggleLayerVisibility('insiden', !isActive);
            });
        }
        
        // Unit toggle
        const toggleUnitBtn = document.getElementById('toggleUnit');
        if (toggleUnitBtn) {
            toggleUnitBtn.addEventListener('click', function() {
                const isActive = this.classList.contains('active');
                toggleLayerVisibility('unit', !isActive);
            });
        }
        
        // GPS toggle
        const toggleGpsBtn = document.getElementById('toggleGps');
        if (toggleGpsBtn) {
            toggleGpsBtn.addEventListener('click', function() {
                const isActive = this.classList.contains('active');
                toggleLayerVisibility('gps', !isActive);
            });
        }
        
        // Evaluasi toggle
        const toggleEvaluasiBtn = document.getElementById('toggleEvaluasi');
        if (toggleEvaluasiBtn) {
            toggleEvaluasiBtn.addEventListener('click', function() {
                const isActive = this.classList.contains('active');
                toggleEvaluasiVisibility(!isActive);
            });
        }
        
        // Reset filter button - juga reset layer visibility
        const btnResetMainFilter = document.getElementById('btnResetMainFilter');
        if (btnResetMainFilter) {
            btnResetMainFilter.addEventListener('click', function() {
                // Reset all layer visibility to true
                toggleLayerVisibility('cctv', true);
                toggleLayerVisibility('hazard', true);
                toggleLayerVisibility('gr', true);
                toggleLayerVisibility('insiden', true);
                toggleLayerVisibility('unit', true);
                toggleLayerVisibility('gps', true);
                toggleEvaluasiVisibility(false);
            });
        }
        
        // SAP category item click handler - Toggle functionality
        const sapCategoryItems = document.querySelectorAll('.gm-category-item');
        sapCategoryItems.forEach(function(item) {
            const span = item.querySelector('span');
            if (span && span.textContent.trim() === 'SAP') {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleSapDisplay();
                });
            }
        });
        
        // Control Room category item click handler
        const controlRoomCategoryItems = document.querySelectorAll('.gm-category-item');
        controlRoomCategoryItems.forEach(function(item) {
            const span = item.querySelector('span');
            if (span && span.textContent.trim() === 'Control Room') {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    showControlRoomModal();
                });
            }
        });
        
        // GPS Unit category item click handler - Toggle functionality
        const gpsUnitCategoryItems = document.querySelectorAll('.gm-category-item');
        gpsUnitCategoryItems.forEach(function(item) {
            const span = item.querySelector('span');
            if (span && span.textContent.trim() === 'Gps Unit') {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleUnitDisplay();
                });
            }
        });
        
        // GPS Orang category item click handler - Toggle functionality
        const gpsOrangCategoryItems = document.querySelectorAll('.gm-category-item');
        gpsOrangCategoryItems.forEach(function(item) {
            const span = item.querySelector('span');
            if (span && span.textContent.trim() === 'Gps Orang') {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleGpsOrangDisplay();
                });
            }
        });
        
        // CCTV category item click handler - Toggle functionality
        const cctvCategoryItems = document.querySelectorAll('.gm-category-item');
        cctvCategoryItems.forEach(function(item) {
            const span = item.querySelector('span');
            if (span && span.textContent.trim() === 'CCTV') {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleCctvDisplay();
                });
                
                // Initialize active state for CCTV since it's visible by default
                if (layerVisibility.cctv && cctvLayer && cctvLayer.getVisible()) {
                    item.classList.add('active');
                }
            }
        });
        
        // Insiden category item click handler - Toggle functionality
        const insidenCategoryItems = document.querySelectorAll('.gm-category-item');
        insidenCategoryItems.forEach(function(item) {
            const span = item.querySelector('span');
            if (span && span.textContent.trim() === 'Insiden') {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleInsidenDisplay();
                });
                
                // Initialize active state for Insiden if visible by default
                if (layerVisibility.insiden && insidenLayer && insidenLayer.getVisible()) {
                    item.classList.add('active');
                }
            }
        });
    });
    
    // Function to toggle SAP display (show/hide)
    function toggleSapDisplay() {
        const sapCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
            const span = item.querySelector('span');
            return span && span.textContent.trim() === 'SAP';
        });
        
        // Ensure hazard layer exists
        if (!hazardLayer) {
            console.error('Hazard layer not initialized');
            return;
        }
        
        // Toggle: jika sudah aktif, sembunyikan. Jika belum aktif, tampilkan
        if (sapDataLoaded && hazardLayer.getVisible()) {
            // Hide SAP - clear markers but keep data in cache
            const source = hazardLayer.getSource();
            const sapCount = source.getFeatures().length;
            source.clear();
            hazardLayer.setVisible(false);
            layerVisibility.hazard = false;
            sapDataLoaded = false;
            
            // Update toggle button if exists
            const toggleHazardBtn = document.getElementById('toggleHazard');
            if (toggleHazardBtn) {
                toggleHazardBtn.classList.remove('active');
            }
            
            // Update visual indicator on SAP category item
            if (sapCategoryItem) {
                sapCategoryItem.classList.remove('active');
            }
            
            console.log('SAP layer hidden (data cached)');
            
            // Show success alert for hiding SAP
            Swal.fire({
                icon: 'info',
                title: 'SAP Disembunyikan',
                text: `${sapCount} titik SAP telah disembunyikan dari peta`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // Show SAP - load data if not cached, or use cached data
            if (sapDataCache.length > 0) {
                // Use cached data
                const source = hazardLayer.getSource();
                source.clear();
                addSapMarkersBatch(sapDataCache);
                hazardLayer.setVisible(true);
                layerVisibility.hazard = true;
                sapDataLoaded = true;
                
                // Update toggle button if exists
                const toggleHazardBtn = document.getElementById('toggleHazard');
                if (toggleHazardBtn) {
                    toggleHazardBtn.classList.add('active');
                }
                
                // Update visual indicator on SAP category item
                if (sapCategoryItem) {
                    sapCategoryItem.classList.add('active');
                }
                
                console.log(`Displayed ${sapDataCache.length} cached SAP points`);
                
                // Show success alert for showing SAP from cache
                Swal.fire({
                    icon: 'success',
                    title: 'SAP Ditampilkan',
                    html: `Menampilkan <strong>${sapDataCache.length}</strong> titik SAP di peta<br><small>Data dari cache</small>`,
                    timer: 2500,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    toast: false,
                    position: 'center'
                });
            } else {
                // Load data from API
                loadAndDisplaySapData();
            }
        }
    }
    
    // Function to toggle Unit display (show/hide)
    function toggleUnitDisplay() {
        const unitCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
            const span = item.querySelector('span');
            return span && span.textContent.trim() === 'Gps Unit';
        });
        
        // Ensure unit vehicle layer exists
        if (!unitVehicleLayer) {
            console.error('Unit vehicle layer not initialized');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Layer unit kendaraan belum diinisialisasi',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            return;
        }
        
        // Toggle: jika sudah aktif, sembunyikan. Jika belum aktif, tampilkan
        if (unitDataLoaded && unitVehicleLayer.getVisible()) {
            // Hide Unit - hide layer but keep data
            unitVehicleLayer.setVisible(false);
            layerVisibility.unit = false;
            unitDataLoaded = false;
            
            // Update toggle button if exists
            const toggleUnitBtn = document.getElementById('toggleUnit');
            if (toggleUnitBtn) {
                toggleUnitBtn.classList.remove('active');
            }
            
            // Update visual indicator on Unit category item
            if (unitCategoryItem) {
                unitCategoryItem.classList.remove('active');
            }
            
            // Get count of visible units
            const source = unitVehicleLayer.getSource();
            const unitCount = source.getFeatures().length;
            
            console.log('Unit layer hidden');
            
            // Show success alert for hiding Unit
            Swal.fire({
                icon: 'info',
                title: 'GPS Unit Disembunyikan',
                text: `${unitCount} unit kendaraan telah disembunyikan dari peta`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // Show Unit - ensure data is loaded and visible
            if (!unitVehicles || unitVehicles.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Data Unit',
                    text: 'Tidak ada data unit kendaraan yang tersedia',
                    timer: 2500,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    toast: false,
                    position: 'center'
                });
                return;
            }
            
            // Update unit markers if needed
            updateUnitVehicleMarkers(unitVehicles);
            
            // Show unit vehicle layer
            unitVehicleLayer.setVisible(true);
            layerVisibility.unit = true;
            unitDataLoaded = true;
            
            // Update toggle button if exists
            const toggleUnitBtn = document.getElementById('toggleUnit');
            if (toggleUnitBtn) {
                toggleUnitBtn.classList.add('active');
            }
            
            // Update visual indicator on Unit category item
            if (unitCategoryItem) {
                unitCategoryItem.classList.add('active');
            }
            
            // Get count of visible units
            const source = unitVehicleLayer.getSource();
            const unitCount = source.getFeatures().length;
            
            console.log(`Displayed ${unitCount} unit vehicles`);
            
            // Show success alert for showing Unit
            Swal.fire({
                icon: 'success',
                title: 'GPS Unit Ditampilkan',
                html: `Menampilkan <strong>${unitCount}</strong> unit kendaraan di peta`,
                timer: 2500,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                toast: false,
                position: 'center'
            });
        }
    }
    
    // Function to toggle GPS Orang display (show/hide)
    function toggleGpsOrangDisplay() {
        const gpsOrangCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
            const span = item.querySelector('span');
            return span && span.textContent.trim() === 'Gps Orang';
        });
        
        // Ensure GPS Orang layer exists
        if (!userGpsLayer) {
            console.error('GPS Orang layer not initialized');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Layer GPS Orang belum diinisialisasi',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            return;
        }
        
        // Toggle: jika sudah aktif, sembunyikan. Jika belum aktif, tampilkan
        if (gpsOrangDataLoaded && userGpsLayer.getVisible()) {
            // Hide GPS Orang - hide layer but keep data
            userGpsLayer.setVisible(false);
            layerVisibility.gps = false;
            gpsOrangDataLoaded = false;
            
            // Update toggle button if exists
            const toggleGpsBtn = document.getElementById('toggleGps');
            if (toggleGpsBtn) {
                toggleGpsBtn.classList.remove('active');
            }
            
            // Update visual indicator on GPS Orang category item
            if (gpsOrangCategoryItem) {
                gpsOrangCategoryItem.classList.remove('active');
            }
            
            // Get count of visible users
            const source = userGpsLayer.getSource();
            const userCount = source.getFeatures().length;
            
            console.log('GPS Orang layer hidden');
            
            // Show success alert for hiding GPS Orang
            Swal.fire({
                icon: 'info',
                title: 'GPS Orang Disembunyikan',
                text: `${userCount} personel telah disembunyikan dari peta`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // Show GPS Orang - load data if not cached, or use cached data
            if (gpsOrangDataCache.length > 0) {
                // Use cached data
                updateGpsOrangMarkers(gpsOrangDataCache);
                userGpsLayer.setVisible(true);
                layerVisibility.gps = true;
                gpsOrangDataLoaded = true;
                
                // Update toggle button if exists
                const toggleGpsBtn = document.getElementById('toggleGps');
                if (toggleGpsBtn) {
                    toggleGpsBtn.classList.add('active');
                }
                
                // Update visual indicator on GPS Orang category item
                if (gpsOrangCategoryItem) {
                    gpsOrangCategoryItem.classList.add('active');
                }
                
                // Get count of visible users
                const source = userGpsLayer.getSource();
                const userCount = source.getFeatures().length;
                
                console.log(`Displayed ${userCount} GPS Orang from cache`);
                
                // Show success alert for showing GPS Orang from cache
                Swal.fire({
                    icon: 'success',
                    title: 'GPS Orang Ditampilkan',
                    html: `Menampilkan <strong>${userCount}</strong> personel di peta<br><small>Data dari cache</small>`,
                    timer: 2500,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    toast: false,
                    position: 'center'
                });
            } else {
                // Load data from API
                loadAndDisplayGpsOrangData();
            }
        }
    }
    
    // Function to toggle CCTV display (show/hide)
    function toggleCctvDisplay() {
        const cctvCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
            const span = item.querySelector('span');
            return span && span.textContent.trim() === 'CCTV';
        });
        
        // Ensure CCTV layer exists
        if (!cctvLayer) {
            console.error('CCTV layer not initialized');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Layer CCTV belum diinisialisasi',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            return;
        }
        
        // Toggle: jika sudah aktif, sembunyikan. Jika belum aktif, tampilkan
        if (cctvLayer.getVisible() && layerVisibility.cctv) {
            // Hide CCTV - hide layer but keep data
            cctvLayer.setVisible(false);
            layerVisibility.cctv = false;
            
            // Update toggle button if exists
            const toggleCctvBtn = document.getElementById('toggleCctv');
            if (toggleCctvBtn) {
                toggleCctvBtn.classList.remove('active');
            }
            
            // Update visual indicator on CCTV category item
            if (cctvCategoryItem) {
                cctvCategoryItem.classList.remove('active');
            }
            
            // Get count of visible CCTV
            const source = cctvLayer.getSource();
            const cctvCount = source.getFeatures().length;
            
            console.log('CCTV layer hidden');
            
            // Show success alert for hiding CCTV
            Swal.fire({
                icon: 'info',
                title: 'CCTV Disembunyikan',
                text: `${cctvCount} kamera CCTV telah disembunyikan dari peta`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // Show CCTV - ensure layer is visible
            cctvLayer.setVisible(true);
            layerVisibility.cctv = true;
            
            // Update toggle button if exists
            const toggleCctvBtn = document.getElementById('toggleCctv');
            if (toggleCctvBtn) {
                toggleCctvBtn.classList.add('active');
            }
            
            // Update visual indicator on CCTV category item
            if (cctvCategoryItem) {
                cctvCategoryItem.classList.add('active');
            }
            
            // Get count of visible CCTV
            const source = cctvLayer.getSource();
            const cctvCount = source.getFeatures().length;
            
            console.log(`Displayed ${cctvCount} CCTV cameras`);
            
            // Show success alert for showing CCTV
            Swal.fire({
                icon: 'success',
                title: 'CCTV Ditampilkan',
                html: `Menampilkan <strong>${cctvCount}</strong> kamera CCTV di peta`,
                timer: 2500,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                toast: false,
                position: 'center'
            });
        }
    }
    
    // Function to toggle Insiden display (show/hide)
    function toggleInsidenDisplay() {
        const insidenCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
            const span = item.querySelector('span');
            return span && span.textContent.trim() === 'Insiden';
        });
        
        // Ensure Insiden layer exists
        if (!insidenLayer) {
            console.error('Insiden layer not initialized');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Layer Insiden belum diinisialisasi',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            return;
        }
        
        // Toggle: jika sudah aktif, sembunyikan. Jika belum aktif, tampilkan
        if (insidenLayer.getVisible() && layerVisibility.insiden) {
            // Hide Insiden - hide layer but keep data
            insidenLayer.setVisible(false);
            layerVisibility.insiden = false;
            
            // Update toggle button if exists
            const toggleInsidenBtn = document.getElementById('toggleInsiden');
            if (toggleInsidenBtn) {
                toggleInsidenBtn.classList.remove('active');
            }
            
            // Update visual indicator on Insiden category item
            if (insidenCategoryItem) {
                insidenCategoryItem.classList.remove('active');
            }
            
            // Get count of visible Insiden
            const source = insidenLayer.getSource();
            const insidenCount = source.getFeatures().length;
            
            console.log('Insiden layer hidden');
            
            // Show success alert for hiding Insiden
            Swal.fire({
                icon: 'info',
                title: 'Insiden Disembunyikan',
                text: `${insidenCount} insiden telah disembunyikan dari peta`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // Show Insiden - ensure layer is visible
            insidenLayer.setVisible(true);
            layerVisibility.insiden = true;
            
            // Update toggle button if exists
            const toggleInsidenBtn = document.getElementById('toggleInsiden');
            if (toggleInsidenBtn) {
                toggleInsidenBtn.classList.add('active');
            }
            
            // Update visual indicator on Insiden category item
            if (insidenCategoryItem) {
                insidenCategoryItem.classList.add('active');
            }
            
            // Get count of visible Insiden
            const source = insidenLayer.getSource();
            const insidenCount = source.getFeatures().length;
            
            console.log(`Displayed ${insidenCount} insiden`);
            
            // Show success alert for showing Insiden
            Swal.fire({
                icon: 'success',
                title: 'Insiden Ditampilkan',
                html: `Menampilkan <strong>${insidenCount}</strong> insiden di peta`,
                timer: 2500,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                toast: false,
                position: 'center'
            });
        }
    }
    
    // Function to load and display GPS Orang data from API
    function loadAndDisplayGpsOrangData() {
        // Show loading indicator
        const gpsOrangCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
            const span = item.querySelector('span');
            return span && span.textContent.trim() === 'Gps Orang';
        });
        
        if (gpsOrangCategoryItem) {
            gpsOrangCategoryItem.style.opacity = '0.6';
            gpsOrangCategoryItem.style.pointerEvents = 'none';
        }
        
        // Ensure GPS Orang layer exists
        if (!userGpsLayer) {
            console.error('GPS Orang layer not initialized');
            if (gpsOrangCategoryItem) {
                gpsOrangCategoryItem.style.opacity = '1';
                gpsOrangCategoryItem.style.pointerEvents = 'auto';
            }
            return;
        }
        
        // Fetch GPS Orang data from API
        fetch('/maps/api/user-gps')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.users && data.users.length > 0) {
                    // Deduplikasi: jika user_id sama, ambil yang terbaru berdasarkan gps_updated_at
                    const uniqueUsers = [];
                    const userMap = new Map();
                    
                    data.users.forEach(user => {
                        const userId = user.user_id || user.id;
                        if (!userId) return;
                        
                        const existingUser = userMap.get(userId);
                        if (!existingUser) {
                            userMap.set(userId, user);
                            uniqueUsers.push(user);
                        } else {
                            const existingTime = existingUser.gps_updated_at || existingUser.gps_created_at || '';
                            const currentTime = user.gps_updated_at || user.gps_created_at || '';
                            
                            if (currentTime > existingTime) {
                                const index = uniqueUsers.indexOf(existingUser);
                                if (index !== -1) {
                                    uniqueUsers[index] = user;
                                    userMap.set(userId, user);
                                }
                            }
                        }
                    });
                    
                    // Cache the data
                    gpsOrangDataCache = uniqueUsers;
                    
                    // Update markers
                    updateGpsOrangMarkers(uniqueUsers);
                    
                    // Show GPS Orang layer
                    userGpsLayer.setVisible(true);
                    layerVisibility.gps = true;
                    gpsOrangDataLoaded = true;
                    
                    // Update toggle button if exists
                    const toggleGpsBtn = document.getElementById('toggleGps');
                    if (toggleGpsBtn) {
                        toggleGpsBtn.classList.add('active');
                    }
                    
                    // Update visual indicator on GPS Orang category item
                    if (gpsOrangCategoryItem) {
                        gpsOrangCategoryItem.classList.add('active');
                    }
                    
                    console.log(`Loaded ${uniqueUsers.length} GPS Orang from API`);
                    
                    // Show success alert for loading GPS Orang from API
                    Swal.fire({
                        icon: 'success',
                        title: 'GPS Orang Berhasil Dimuat',
                        html: `Menampilkan <strong>${uniqueUsers.length}</strong> personel di peta<br><small>Data dari database</small>`,
                        timer: 3000,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        toast: false,
                        position: 'center'
                    });
                    
                    if (gpsOrangCategoryItem) {
                        gpsOrangCategoryItem.style.opacity = '1';
                        gpsOrangCategoryItem.style.pointerEvents = 'auto';
                    }
                } else {
                    console.warn('No GPS Orang data received from API');
                    
                    // Show warning alert for no data
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Data GPS Orang',
                        text: 'Tidak ada data GPS personel yang ditemukan dari database',
                        timer: 2500,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        toast: false,
                        position: 'center'
                    });
                    
                    if (gpsOrangCategoryItem) {
                        gpsOrangCategoryItem.style.opacity = '1';
                        gpsOrangCategoryItem.style.pointerEvents = 'auto';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading GPS Orang data:', error);
                
                // Show error alert
                Swal.fire({
                    icon: 'error',
                    title: 'Error Memuat GPS Orang',
                    text: 'Terjadi kesalahan saat memuat data GPS personel dari server',
                    timer: 3000,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    toast: false,
                    position: 'center'
                });
                
                if (gpsOrangCategoryItem) {
                    gpsOrangCategoryItem.style.opacity = '1';
                    gpsOrangCategoryItem.style.pointerEvents = 'auto';
                }
            });
    }
    
    // Function to show Control Room modal
    function showControlRoomModal() {
        // Initialize control room data from ALL CCTV (tanpa filter pengawas)
        // Gunakan cctvLocationsForControlRoom untuk mendapatkan semua control room
        if (allControlRooms.length === 0) {
            // Group CCTV by control_room dari data lengkap (tanpa filter pengawas)
            const controlRoomMap = {};
            
            (cctvLocationsForControlRoom || []).forEach(cctv => {
                const controlRoom = (cctv.control_room && cctv.control_room.trim() !== '') 
                    ? cctv.control_room.trim() 
                    : null;
                
                // Skip if control_room is null or empty
                if (!controlRoom) return;
                
                if (!controlRoomMap[controlRoom]) {
                    controlRoomMap[controlRoom] = {
                        name: controlRoom,
                        cctv_list: []
                    };
                }
                
                controlRoomMap[controlRoom].cctv_list.push(cctv);
            });
            
            // Convert to array and sort by name
            allControlRooms = Object.values(controlRoomMap)
                .sort((a, b) => a.name.localeCompare(b.name));
        }
        
        const modalBody = document.getElementById('controlRoomModalBody');
        if (!modalBody) return;
        
        if (allControlRooms.length === 0) {
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <i class="material-icons-outlined" style="font-size: 48px; color: #9ca3af; margin-bottom: 16px;">meeting_room</i>
                    <p class="text-muted">Tidak ada data Control Room</p>
                </div>
            `;
        } else {
            // Build HTML for control room list with toggle switches
            const html = `
                <div class="mb-3">
                    <p class="text-muted mb-3">Pilih Control Room untuk menampilkan CCTV di peta:</p>
                    <div class="list-group">
                        ${allControlRooms.map((controlRoom, index) => {
                            const name = controlRoom.name || 'Unknown';
                            const cctvCount = controlRoom.cctv_list ? controlRoom.cctv_list.length : 0;
                            const isActive = activeControlRooms.includes(name);
                            
                            return `
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${escapeHtml(name)}</h6>
                                        <small class="text-muted">${cctvCount} CCTV</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="controlRoomToggle${index}" 
                                               data-control-room="${escapeHtml(name)}"
                                               ${isActive ? 'checked' : ''}
                                               onchange="toggleControlRoom('${escapeHtml(name)}', this.checked)">
                                        <label class="form-check-label" for="controlRoomToggle${index}"></label>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="material-icons-outlined me-2" style="font-size: 18px; vertical-align: middle;">info</i>
                    <small>Gunakan toggle untuk menampilkan/menyembunyikan CCTV dari Control Room yang dipilih. Klik "Terapkan Filter" untuk mengupdate peta.</small>
                </div>
            `;
            modalBody.innerHTML = html;
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('controlRoomModal'));
        modal.show();
    }
    
    // Function to toggle control room (make it globally accessible)
    window.toggleControlRoom = function(controlRoomName, isActive) {
        if (isActive) {
            // Add to active list if not already there
            if (!activeControlRooms.includes(controlRoomName)) {
                activeControlRooms.push(controlRoomName);
            }
        } else {
            // Remove from active list
            activeControlRooms = activeControlRooms.filter(cr => cr !== controlRoomName);
        }
    };
    
    // Function to apply control room filter (make it globally accessible)
    window.applyControlRoomFilter = function() {
        if (!cctvLayer) {
            console.error('CCTV layer not initialized');
            return;
        }
        
        const source = cctvLayer.getSource();
        
        // Store original style function if not already stored
        if (!cctvLayer.get('originalStyleFunction')) {
            cctvLayer.set('originalStyleFunction', cctvLayer.getStyle());
        }
        
        if (activeControlRooms.length === 0) {
            // If no control room selected, restore to default (filter by auth pengawas)
            // Clear all features and re-add from default data (cctvLocationsForMap)
            source.clear();
            addCctvMarkersFromData(cctvLocationsForMap || []);
            
            // Restore original style
            const originalStyle = cctvLayer.get('originalStyleFunction');
            if (originalStyle) {
                cctvLayer.setStyle(originalStyle);
            }
            
            console.log('Restored to default CCTV (filter by auth pengawas)');
            
            // Show success alert
            Swal.fire({
                icon: 'success',
                title: 'Filter Direset',
                text: 'CCTV kembali ditampilkan sesuai dengan filter pengawas default',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // Filter by Control Room: Replace all CCTV with only selected control rooms
            // Use ALL CCTV data (cctvLocationsForMapAll) to get CCTV from selected control rooms
            source.clear();
            
            // Filter CCTV from all data based on selected control rooms
            const filteredCctv = (cctvLocationsForMapAll || []).filter(cctv => {
                const controlRoom = cctv.control_room ? cctv.control_room.trim() : null;
                return controlRoom && activeControlRooms.includes(controlRoom);
            });
            
            // Add filtered CCTV to map
            addCctvMarkersFromData(filteredCctv);
            
            // Restore original style
            const originalStyle = cctvLayer.get('originalStyleFunction');
            if (originalStyle) {
                cctvLayer.setStyle(originalStyle);
            }
            
            console.log(`Control Room filter applied: ${filteredCctv.length} CCTV from ${activeControlRooms.length} control room(s)`);
            console.log('Active Control Rooms:', activeControlRooms);
            
            // Show success alert
            const controlRoomNames = activeControlRooms.join(', ');
            Swal.fire({
                icon: 'success',
                title: 'Filter Berhasil Diterapkan',
                html: `Menampilkan <strong>${filteredCctv.length}</strong> CCTV dari <strong>${activeControlRooms.length}</strong> Control Room:<br><small>${escapeHtml(controlRoomNames)}</small>`,
                timer: 3000,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                toast: false,
                position: 'center'
            });
        }
        
        // Force layer redraw
        cctvLayer.changed();
        
        // Update visual indicator on Control Room category item
        const controlRoomCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
            const span = item.querySelector('span');
            return span && span.textContent.trim() === 'Control Room';
        });
        
        if (controlRoomCategoryItem) {
            if (activeControlRooms.length > 0) {
                controlRoomCategoryItem.classList.add('active');
            } else {
                controlRoomCategoryItem.classList.remove('active');
            }
        }
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('controlRoomModal'));
        if (modal) {
            modal.hide();
        }
    };
    
    // Function to load and display SAP data from API
    function loadAndDisplaySapData() {
        // Show loading indicator
        const sapCategoryItem = Array.from(document.querySelectorAll('.gm-category-item')).find(item => {
            const span = item.querySelector('span');
            return span && span.textContent.trim() === 'SAP';
        });
        
        if (sapCategoryItem) {
            sapCategoryItem.style.opacity = '0.6';
            sapCategoryItem.style.pointerEvents = 'none';
        }
        
        // Ensure hazard layer exists
        if (!hazardLayer) {
            console.error('Hazard layer not initialized');
            if (sapCategoryItem) {
                sapCategoryItem.style.opacity = '1';
                sapCategoryItem.style.pointerEvents = 'auto';
            }
            return;
        }
        
        // Fetch SAP data from API
        fetch('/full-maps/api/sap-data?limit=500')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    // Clear existing SAP markers
                    const source = hazardLayer.getSource();
                    source.clear();
                    
                    // Cache the data
                    sapDataCache = data.data;
                    
                    // Add new SAP markers
                    addSapMarkersBatch(data.data);
                    
                    // Show hazard layer
                    hazardLayer.setVisible(true);
                    layerVisibility.hazard = true;
                    sapDataLoaded = true;
                    
                    // Update toggle button if exists
                    const toggleHazardBtn = document.getElementById('toggleHazard');
                    if (toggleHazardBtn) {
                        toggleHazardBtn.classList.add('active');
                    }
                    
                    // Update visual indicator on SAP category item
                    if (sapCategoryItem) {
                        sapCategoryItem.classList.add('active');
                    }
                    
                    console.log(`Loaded ${data.count} SAP points from ClickHouse`);
                    
                    // Show success alert for loading SAP from API
                    Swal.fire({
                        icon: 'success',
                        title: 'SAP Berhasil Dimuat',
                        html: `Menampilkan <strong>${data.count}</strong> titik SAP di peta<br><small>Data dari ClickHouse</small>`,
                        timer: 3000,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        toast: false,
                        position: 'center'
                    });
                    
                    // Show success message (optional)
                    if (sapCategoryItem) {
                        sapCategoryItem.style.opacity = '1';
                        sapCategoryItem.style.pointerEvents = 'auto';
                    }
                } else {
                    console.warn('No SAP data received from API');
                    
                    // Show warning alert for no data
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Ada Data SAP',
                        text: 'Tidak ada data SAP yang ditemukan dari database',
                        timer: 2500,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6',
                        toast: false,
                        position: 'center'
                    });
                    
                    if (sapCategoryItem) {
                        sapCategoryItem.style.opacity = '1';
                        sapCategoryItem.style.pointerEvents = 'auto';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading SAP data:', error);
                
                // Show error alert
                Swal.fire({
                    icon: 'error',
                    title: 'Error Memuat SAP',
                    text: 'Terjadi kesalahan saat memuat data SAP dari server',
                    timer: 3000,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    toast: false,
                    position: 'center'
                });
                
                if (sapCategoryItem) {
                    sapCategoryItem.style.opacity = '1';
                    sapCategoryItem.style.pointerEvents = 'auto';
                }
            });
    }
    
    // Global variables for evaluation
    let evaluationOverlays = [];
    let evaluationEnabled = false;
    
    // Function to toggle evaluasi visibility
    function toggleEvaluasiVisibility(show) {
        evaluationEnabled = show;
        const toggleBtn = document.getElementById('toggleEvaluasi');
        if (toggleBtn) {
            if (show) {
                toggleBtn.classList.add('active');
                showEvaluationAlerts();
            } else {
                toggleBtn.classList.remove('active');
                hideEvaluationAlerts();
            }
        }
    }
    
    // Function to normalize location name for better matching
    function normalizeLocationName(name) {
        if (!name) return '';
        return name.toLowerCase()
            .trim()
            .replace(/\([^)]*\)/g, '') // Remove content in parentheses like "(B8)"
            .replace(/\[[^\]]*\]/g, '') // Remove content in square brackets
            .replace(/\s+/g, ' ') // Multiple spaces to single space
            .replace(/[_-]/g, ' ') // Replace underscore and dash with space
            .replace(/[^\w\s]/g, '') // Remove special characters except word and space
            .replace(/\b(area|lokasi|zone|zona|site|tempat)\b/gi, '') // Remove common location words
            .trim();
    }
    
    // Function for simple flexible matching (less aggressive normalization)
    function simpleLocationMatch(areaName, cctvLocation) {
        if (!areaName || !cctvLocation) return false;
        
        const areaLower = (areaName || '').toLowerCase().trim();
        const cctvLower = (cctvLocation || '').toLowerCase().trim();
        
        // Direct match
        if (areaLower === cctvLower) return true;
        
        // Contains check
        if (areaLower.includes(cctvLower) || cctvLower.includes(areaLower)) {
            return true;
        }
        
        // Remove parentheses and brackets, then check
        const areaClean = areaLower.replace(/\([^)]*\)/g, '').replace(/\[[^\]]*\]/g, '').trim();
        const cctvClean = cctvLower.replace(/\([^)]*\)/g, '').replace(/\[[^\]]*\]/g, '').trim();
        
        if (areaClean && cctvClean) {
            if (areaClean === cctvClean) return true;
            if (areaClean.includes(cctvClean) || cctvClean.includes(areaClean)) {
                return true;
            }
        }
        
        return false;
    }

    // Function to extract key words from location name (for flexible matching)
    function extractLocationKeywords(name) {
        if (!name) return [];
        const normalized = normalizeLocationName(name);
        // Split by space and filter out common words
        const words = normalized.split(/\s+/)
            .filter(word => word.length > 1) // Only words with more than 1 character
            .filter(word => !['the', 'and', 'or', 'of', 'in', 'on', 'at'].includes(word.toLowerCase()));
        return words;
    }

    // Function to check if two location names match (flexible matching)
    function isLocationMatch(areaName, cctvLocation) {
        if (!areaName || !cctvLocation) return false;
        
        const areaLower = (areaName || '').toLowerCase().trim();
        const cctvLower = (cctvLocation || '').toLowerCase().trim();
        
        // Direct match (case insensitive)
        if (areaLower === cctvLower) return true;
        
        // Simple contains check (most flexible)
        if (areaLower.includes(cctvLower) || cctvLower.includes(areaLower)) {
            return true;
        }
        
        // Normalize and check
        const areaNormalized = normalizeLocationName(areaName);
        const cctvNormalized = normalizeLocationName(cctvLocation);
        
        // Direct match after normalization
        if (areaNormalized && cctvNormalized && areaNormalized === cctvNormalized) return true;
        
        // Check if one contains the other after normalization
        if (areaNormalized && cctvNormalized) {
            if (areaNormalized.includes(cctvNormalized) || cctvNormalized.includes(areaNormalized)) {
                return true;
            }
        }
        
        // Extract keywords and check if they match
        const areaKeywords = extractLocationKeywords(areaName);
        const cctvKeywords = extractLocationKeywords(cctvLocation);
        
        if (areaKeywords.length > 0 && cctvKeywords.length > 0) {
            // Check if at least one keyword matches
            const hasMatchingKeyword = areaKeywords.some(keyword => 
                cctvKeywords.some(cctvKeyword => 
                    keyword.includes(cctvKeyword) || cctvKeyword.includes(keyword) || keyword === cctvKeyword
                )
            );
            
            if (hasMatchingKeyword) {
                return true;
            }
            
            // Check if at least 50% of keywords match
            const matchingKeywords = areaKeywords.filter(keyword => 
                cctvKeywords.some(cctvKeyword => 
                    keyword.includes(cctvKeyword) || cctvKeyword.includes(keyword) || keyword === cctvKeyword
                )
            );
            
            if (matchingKeywords.length > 0) {
                const matchRatio = matchingKeywords.length / Math.max(areaKeywords.length, cctvKeywords.length);
                if (matchRatio >= 0.3) { // Lowered threshold from 0.5 to 0.3
                    return true;
                }
            }
        }
        
        // Check if key words like "PIT J" exist in both (case insensitive)
        const pitPattern = /pit\s+[a-z0-9]+/gi;
        const areaPits = areaLower.match(pitPattern) || [];
        const cctvPits = cctvLower.match(pitPattern) || [];
        
        if (areaPits.length > 0 && cctvPits.length > 0) {
            // Check if any pit identifier matches
            for (const areaPit of areaPits) {
                for (const cctvPit of cctvPits) {
                    const areaPitNorm = normalizeLocationName(areaPit);
                    const cctvPitNorm = normalizeLocationName(cctvPit);
                    if (areaPitNorm === cctvPitNorm || areaPitNorm.includes(cctvPitNorm) || cctvPitNorm.includes(areaPitNorm)) {
                        return true;
                    }
                }
            }
        }
        
        // Additional check: extract single letter/number identifiers (like "J", "CD", etc)
        const identifierPattern = /\b([a-z]{1,3}|[0-9]+)\b/gi;
        const areaIdentifiers = areaLower.match(identifierPattern) || [];
        const cctvIdentifiers = cctvLower.match(identifierPattern) || [];
        
        if (areaIdentifiers.length > 0 && cctvIdentifiers.length > 0) {
            // Check if any identifier matches
            for (const areaId of areaIdentifiers) {
                for (const cctvId of cctvIdentifiers) {
                    if (areaId.toLowerCase() === cctvId.toLowerCase()) {
                        // If identifiers match, check if context is similar (both have "pit" or similar)
                        if (areaLower.includes('pit') && cctvLower.includes('pit')) {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    // Function to check if coordinate is inside polygon geometry
    function isCoordinateInGeometry(latitude, longitude, geometry) {
        if (!latitude || !longitude || !geometry) return false;
        
        try {
            // Convert lat/lng to map projection (EPSG:3857)
            const coordinate = ol.proj.fromLonLat([parseFloat(longitude), parseFloat(latitude)]);
            
            const geometryType = geometry.getType();
            
            // For Polygon and MultiPolygon, check if point is inside
            if (geometryType === 'Polygon' || geometryType === 'MultiPolygon') {
                // Use intersectsCoordinate for quick check
                if (geometry.intersectsCoordinate(coordinate)) {
                    return true;
                }
                
                // For more accurate check, create a point and check if it's inside polygon
                const point = new ol.geom.Point(coordinate);
                
                // Check if point intersects with polygon using intersects method
                // Note: intersectsGeometry doesn't exist in OpenLayers, use intersectsCoordinate instead
                try {
                    if (geometry.intersectsCoordinate(coordinate)) {
                        return true;
                    }
                } catch (e) {
                    // Fallback: check if coordinate is within extent
                    const extent = geometry.getExtent();
                    if (extent && ol.extent.containsCoordinate(extent, coordinate)) {
                        // Additional check: use ray casting algorithm for more accurate point-in-polygon
                        // For now, if it's in extent, we'll consider it as inside
                        return true;
                    }
                }
                
                // Also check if point is very close to polygon boundary (within 10 meters tolerance)
                // This handles cases where coordinate is slightly outside due to GPS accuracy
                try {
                    const closestPoint = geometry.getClosestPoint(coordinate);
                    const distance = ol.coordinate.distance(coordinate, closestPoint);
                    // Convert distance from map units to meters (approximate: 1 map unit ≈ 1 meter at equator)
                    // For more accuracy, we can use ol.sphere.getDistance but it requires lon/lat
                    const distanceInMeters = distance; // Approximate conversion
                    if (distanceInMeters < 10) { // 10 meters tolerance
                        return true;
                    }
                } catch (e) {
                    // Ignore error in closest point calculation
                }
            } else {
                // For other geometry types, use intersectsCoordinate
                if (geometry.intersectsCoordinate(coordinate)) {
                    return true;
                }
            }
            
            return false;
        } catch (e) {
            console.warn('Error checking coordinate in geometry:', e);
            return false;
        }
    }
    
    // Function to check if area has SAP report today
    // SAP terdiri dari: Hazard, Inspeksi, Observasi, Coaching, OAK
    // Semua jenis SAP sudah termasuk dalam sapDataForSidebar dari getSapDataFromClickHouse()
    // Parameters:
    //   - areaType: 'area_kerja' or 'area_cctv'
    //   - idLokasi: ID lokasi untuk area kerja
    //   - lokasiName: Nama lokasi area kerja atau area CCTV
    //   - nomorCctv: Nomor CCTV untuk area CCTV
    //   - cctvName: Nama CCTV untuk area CCTV
    //   - featureGeometry: OpenLayers geometry dari feature area (optional, untuk pengecekan koordinat)
    function hasSapReportToday(areaType, idLokasi, lokasiName, nomorCctv, cctvName, featureGeometry) {
        // Logging untuk debugging
        const logPrefix = `[SAP Check] ${areaType} - ${lokasiName || cctvName || nomorCctv || 'Unknown'}:`;
        console.log(`${logPrefix} Memulai pengecekan...`);
        
        if (!sapDataForSidebar || sapDataForSidebar.length === 0) {
            console.log(`${logPrefix} Tidak ada data SAP di sidebar`);
            return false;
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const todayStr = today.toISOString().split('T')[0];
        
        // Filter SAP data for today
        // Mengecek semua jenis SAP: Hazard, Inspeksi (INSPEKSI_HAZARD), Observasi, Coaching, OAK
        const sapToday = sapDataForSidebar.filter(sap => {
            if (!sap.tanggal_pelaporan && !sap.detected_at) return false;
            try {
                const sapDate = new Date(sap.tanggal_pelaporan || sap.detected_at);
                sapDate.setHours(0, 0, 0, 0);
                const sapDateStr = sapDate.toISOString().split('T')[0];
                return sapDateStr === todayStr;
            } catch (e) {
                return false;
            }
        });
        
        console.log(`${logPrefix} Total SAP hari ini: ${sapToday.length} dari ${sapDataForSidebar.length} total`);
        
        if (sapToday.length === 0) {
            console.log(`${logPrefix} Tidak ada SAP hari ini`);
            return false;
        }
        
        // Normalize area location name
        let normalizedAreaName = '';
        if (areaType === 'area_kerja') {
            normalizedAreaName = normalizeLocationName(lokasiName || '');
        } else if (areaType === 'area_cctv') {
            normalizedAreaName = normalizeLocationName(cctvName || nomorCctv || '');
        }
        
        console.log(`${logPrefix} Nama area dinormalisasi: "${normalizedAreaName}"`);
        
        // Check if any SAP (dari semua jenis: Hazard, Inspeksi, Observasi, Coaching, OAK) matches the area
        let matchCount = 0;
        let coordinateMatchCount = 0;
        let nameMatchCount = 0;
        
        for (const sap of sapToday) {
            const sapLokasi = normalizeLocationName(sap.lokasi || '');
            const sapDetailLokasi = normalizeLocationName(sap.detail_lokasi || '');
            const jenisLaporan = sap.jenis_laporan || sap.source_type || sap.type || 'SAP';
            
            let matched = false;
            let matchReason = '';
            
            // Method 1: Check coordinate geografis (jika ada geometry dan koordinat SAP)
            if (featureGeometry) {
                const sapLat = sap.latitude || (sap.location && sap.location.lat) || null;
                const sapLng = sap.longitude || (sap.location && sap.location.lng) || null;
                
                if (sapLat && sapLng && !isNaN(parseFloat(sapLat)) && !isNaN(parseFloat(sapLng))) {
                    if (isCoordinateInGeometry(parseFloat(sapLat), parseFloat(sapLng), featureGeometry)) {
                        matched = true;
                        matchReason = `Koordinat (${sapLat}, ${sapLng})`;
                        coordinateMatchCount++;
                        console.log(`${logPrefix} ✓ MATCH via koordinat - ${jenisLaporan} #${sap.task_number || 'N/A'}: ${matchReason}`);
                        return true; // Return immediately if coordinate matches
                    }
                }
            }
            
            // Method 2: Check nama lokasi (normalized string matching)
            if (!matched && normalizedAreaName) {
                // Check if normalized area name matches SAP location
                const nameMatch = (
                    (sapLokasi && (sapLokasi.includes(normalizedAreaName) || normalizedAreaName.includes(sapLokasi))) ||
                    (sapDetailLokasi && (sapDetailLokasi.includes(normalizedAreaName) || normalizedAreaName.includes(sapDetailLokasi)))
                );
                
                if (nameMatch) {
                    matched = true;
                    matchReason = `Nama lokasi: "${sapLokasi || sapDetailLokasi}"`;
                    nameMatchCount++;
                    console.log(`${logPrefix} ✓ MATCH via nama - ${jenisLaporan} #${sap.task_number || 'N/A'}: ${matchReason}`);
                    return true; // Return immediately if name matches
                }
            }
        }
        
        // Log summary
        console.log(`${logPrefix} Tidak ada match. Pengecekan: ${sapToday.length} SAP, ${coordinateMatchCount} match koordinat, ${nameMatchCount} match nama`);
        
        return false;
    }
    
    // Function to show evaluation alerts on map
    function showEvaluationAlerts() {
        // Clear existing overlays
        hideEvaluationAlerts();
        
        if (!evaluationEnabled) {
            console.log('Evaluation toggle is off');
            return;
        }
        
        if (!window.areaCctvLayers || !window.areaKerjaLayers) {
            console.log('Area layers not loaded yet, retrying...');
            // Retry after a short delay
            setTimeout(() => {
                if (evaluationEnabled) {
                    showEvaluationAlerts();
                }
            }, 1000);
            return;
        }
        
        const allLayers = [...(window.areaCctvLayers || []), ...(window.areaKerjaLayers || [])];
        let alertCount = 0;
        
        allLayers.forEach(layer => {
            // Skip if layer is not visible
            if (!layer.getVisible()) return;
            
            const source = layer.getSource();
            if (!source) return;
            
            const features = source.getFeatures();
            features.forEach(feature => {
                const props = feature.getProperties();
                const hasNomorCctv = 'nomor_cctv' in props;
                const hasIdLokasi = 'id_lokasi' in props;
                
                let areaType = null;
                let idLokasi = null;
                let lokasiName = null;
                let nomorCctv = null;
                let cctvName = null;
                
                if (hasNomorCctv) {
                    areaType = 'area_cctv';
                    nomorCctv = (props.nomor_cctv && props.nomor_cctv !== null && props.nomor_cctv !== 'null') ? props.nomor_cctv : null;
                    cctvName = (props.nama_cctv && props.nama_cctv !== null && props.nama_cctv !== 'null') ? props.nama_cctv : null;
                    lokasiName = props.coverage_lokasi || props.lokasi_pemasangan || props.coverage_detail_lokasi || props.lokasi || '';
                } else if (hasIdLokasi) {
                    areaType = 'area_kerja';
                    idLokasi = (props.id_lokasi && props.id_lokasi !== null && props.id_lokasi !== 'null') ? props.id_lokasi : null;
                    lokasiName = (props.lokasi && props.lokasi !== null && props.lokasi !== 'null') ? props.lokasi : null;
                }
                
                if (!areaType) return;
                
                // Get geometry for coordinate checking
                const geometry = feature.getGeometry();
                
                // Check if area has SAP report today
                // Pass geometry untuk pengecekan koordinat geografis
                const hasSap = hasSapReportToday(areaType, idLokasi, lokasiName, nomorCctv, cctvName, geometry);
                
                if (!hasSap) {
                    // Get geometry center for label
                    if (geometry) {
                        const extent = geometry.getExtent();
                        const center = ol.extent.getCenter(extent);
                        
                        // Create label element (permanent text label)
                        const labelElement = document.createElement('div');
                        labelElement.className = 'evaluation-alert-label';
                        labelElement.style.cssText = `
                            background: rgba(239, 68, 68, 0.95);
                            color: #ffffff;
                            border: 2px solid #dc2626;
                            border-radius: 6px;
                            padding: 6px 12px;
                            font-size: 12px;
                            font-weight: 600;
                            white-space: nowrap;
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
                            z-index: 1000;
                            pointer-events: none;
                            text-align: center;
                            display: inline-block;
                            line-height: 1.4;
                        `;
                        
                        const areaName = hasNomorCctv 
                            ? (cctvName || nomorCctv || 'Area CCTV')
                            : (lokasiName || 'Area Kerja');
                        
                        labelElement.innerHTML = `
                            <span style="display: inline-flex; align-items: center; gap: 4px;">
                                <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">warning</i>
                                <span>Area ini belum ada laporan SAP</span>
                            </span>
                        `;
                        
                        const overlay = new ol.Overlay({
                            element: labelElement,
                            position: center,
                            positioning: 'center-center',
                            stopEvent: false,
                            offset: [0, -20]
                        });
                        
                        map.addOverlay(overlay);
                        evaluationOverlays.push(overlay);
                        alertCount++;
                    }
                }
            });
        });
        
        if (alertCount > 0) {
            console.log(`✓ Evaluation alerts: ${alertCount} area(s) tanpa laporan SAP hari ini`);
        } else {
            console.log('✓ Semua area sudah memiliki laporan SAP hari ini');
        }
    }
    
    // Function to hide evaluation alerts
    function hideEvaluationAlerts() {
        evaluationOverlays.forEach(overlay => {
            map.removeOverlay(overlay);
        });
        evaluationOverlays = [];
    }

    // Sidebar Panel Management - variabel sudah didefinisikan di atas
    // Initialize sidebar data
    // Data CCTV diambil langsung dari database (cctvLocations), bukan dari WMS atau GeoJSON
    function initializeSidebarData() {
        // Pastikan menggunakan data dari database
        filteredSidebarData.cctv = [...(cctvLocations || [])];
        // SAP data sudah di-load oleh loadSapDataByWeek() berdasarkan week filter
        // Jangan overwrite jika sudah ada data dari week filter
        // Gunakan sapDataForSidebar jika tersedia (semua data), jika tidak gunakan sapData
        if (filteredSidebarData.sap.length === 0) {
            if (typeof sapDataForSidebar !== 'undefined' && sapDataForSidebar && sapDataForSidebar.length > 0) {
                filteredSidebarData.sap = [...sapDataForSidebar]; // Semua data untuk sidebar
            } else if (sapData && sapData.length > 0) {
                filteredSidebarData.sap = [...sapData];
            }
        }
        filteredSidebarData.insiden = [...(insidenDataset || [])];
        // Update unit data jika sudah ada
        if (unitVehicles && unitVehicles.length > 0) {
            filteredSidebarData.unit = [...unitVehicles];
            console.log('Unit data initialized in sidebar:', filteredSidebarData.unit.length, 'units');
        } else {
            filteredSidebarData.unit = [];
            console.log('Unit data is empty, will be loaded by refreshUnitVehicles()');
        }
        // GPS data removed - no longer used
        
        // Initialize Control Room data - group CCTV by control_room
        initializeControlRoomData();
        
        updateTabCounts();
        renderSidebarTab(currentSidebarTab);
        
        // Jika tab unit aktif, render list unit
        if (currentSidebarTab === 'unit' && filteredSidebarData.unit.length > 0) {
            renderUnitList(filteredSidebarData.unit);
        }
    }
    
    // Update tab counts
    function updateTabCounts() {
        const cctvCount = document.getElementById('cctvTabCount');
        const sapCount = document.getElementById('sapTabCount');
        const insidenCount = document.getElementById('insidenTabCount');
        const unitCount = document.getElementById('unitTabCount');
        // GPS tab removed - no longer used
        const pjaCount = document.getElementById('pjaTabCount');
        
        // Update icon toolbar badges
        const iconToolbarBadges = {
            'cctvTabCount': 'iconToolbarCctvCount',
            'sapTabCount': 'iconToolbarSapCount',
            'insidenTabCount': 'iconToolbarInsidenCount',
            'unitTabCount': 'iconToolbarUnitCount',
            'pjaTabCount': 'iconToolbarPjaCount',
            'controlroomTabCount': 'iconToolbarControlroomCount'
        };
        
        // Sync counts to icon toolbar badges
        Object.keys(iconToolbarBadges).forEach(tabCountId => {
            const tabCountEl = document.getElementById(tabCountId);
            const iconBadgeEl = document.getElementById(iconToolbarBadges[tabCountId]);
            if (tabCountEl && iconBadgeEl) {
                const count = tabCountEl.textContent.trim();
                if (count && count !== '0' && count !== '...') {
                    iconBadgeEl.textContent = count;
                } else {
                    iconBadgeEl.textContent = '';
                }
            }
        });
        
        if (cctvCount) cctvCount.textContent = filteredSidebarData.cctv.length;
        // Untuk SAP, gunakan semua data per week untuk count (bukan hanya data hari ini)
        if (sapCount) {
            const sapCountValue = (typeof sapDataAllWeek !== 'undefined' && sapDataAllWeek.length > 0) 
                ? sapDataAllWeek.length 
                : filteredSidebarData.sap.length;
            sapCount.textContent = sapCountValue;
        }
        if (insidenCount) insidenCount.textContent = filteredSidebarData.insiden.length;
        if (unitCount) unitCount.textContent = filteredSidebarData.unit.length;
        // GPS tab removed - no longer used
        const controlroomCount = document.getElementById('controlroomTabCount');
        if (controlroomCount) controlroomCount.textContent = filteredSidebarData.controlroom.length;
        if (pjaCount) pjaCount.textContent = filteredSidebarData.pja.length;
        
        // Update icon toolbar badges after updating tab counts
        Object.keys(iconToolbarBadges).forEach(tabCountId => {
            const tabCountEl = document.getElementById(tabCountId);
            const iconBadgeEl = document.getElementById(iconToolbarBadges[tabCountId]);
            if (tabCountEl && iconBadgeEl) {
                const count = tabCountEl.textContent.trim();
                if (count && count !== '0' && count !== '...') {
                    iconBadgeEl.textContent = count;
                } else {
                    iconBadgeEl.textContent = '';
                }
            }
        });
    }
    
    // Get avatar color based on first letter
    function getAvatarColor(letter) {
        const colors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
            '#ec4899', '#06b6d4', '#f97316', '#84cc16', '#6366f1'
        ];
        const index = (letter.charCodeAt(0) - 65) % colors.length;
        return colors[index >= 0 ? index : 0];
    }
    
    // Get first letter for avatar
    function getFirstLetter(text) {
        if (!text) return '?';
        const firstChar = text.trim().charAt(0).toUpperCase();
        return /[A-Z0-9]/.test(firstChar) ? firstChar : '?';
    }
    
    // Format timestamp
    function formatTimestamp(dateString) {
        if (!dateString) return '';
        
        try {
            const date = new Date(dateString);
            // Kurangi 7 jam dari waktu database
            date.setHours(date.getHours() - 7);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${day} ${month} ${year} ${hours}:${minutes} WITA`;
        } catch (e) {
            return dateString;
        }
    }
    
    // Format date with time (for history)
    function formatDateWIB(dateString) {
        if (!dateString) return 'N/A';
        
        try {
            const date = new Date(dateString);
            // Kurangi 7 jam dari waktu database
            date.setHours(date.getHours() - 7);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${day} ${month} ${year}, ${hours}:${minutes} WITA`;
        } catch (e) {
            return dateString;
        }
    }
    
    // Render CCTV list
    // Data CCTV diambil langsung dari database, bukan dari WMS atau GeoJSON
    function renderCctvList(data) {
        const container = document.getElementById('cctvList');
        if (!container) return;
        
        // Pastikan data berasal dari database (cctvLocations)
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="material-icons-outlined">videocam_off</i>
                    <p>Tidak ada data CCTV</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = data.map((cctv, index) => {
            // Data dari database: nama_cctv, no_cctv, id, dll
            const name = cctv.name || cctv.nama_cctv || cctv.no_cctv || `CCTV ${cctv.id}`;
            const id = cctv.no_cctv || cctv.nomor_cctv || cctv.id || '';
            const fullId = cctv.id || '';
            const firstLetter = getFirstLetter(name);
            const avatarColor = getAvatarColor(firstLetter);
            
            // Gunakan kategori_area_tercapture dari database
            const kategoriArea = cctv.kategori_area_tercapture || '';
            
            return `
                <div class="sidebar-list-item" data-type="cctv" data-id="${cctv.id}" data-index="${index}" data-hazard-status="loading">
                    <div class="sidebar-list-item-header">
                        <div class="list-item-avatar" style="background-color: ${avatarColor};">
                            ${firstLetter}
                        </div>
                        <div class="list-item-content">
                            <div class="list-item-title">${name}</div>
                            <div class="list-item-subtitle">${id ? `${id}${fullId ? ` (${fullId})` : ''}` : `ID: ${fullId || index + 1}`}</div>
                            ${kategoriArea ? `<div class="list-item-time">${kategoriArea}</div>` : ''}
                        </div>
                        <div class="cctv-hazard-status-icon loading" title="Memeriksa status hazard inspeksi...">
                            <i class="material-icons-outlined" style="font-size: 14px;">hourglass_empty</i>
                        </div>
                        <i class="material-icons-outlined list-item-expand-icon">expand_more</i>
                    </div>
                    <div class="cctv-detail-section">
                        <div class="cctv-detail-loading">
                            <i class="material-icons-outlined" style="font-size: 24px; margin-bottom: 8px; opacity: 0.5;">hourglass_empty</i>
                            <div>Memuat detail...</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        // Load hazard status for all CCTV
        loadCctvHazardStatus(data.map(c => c.id));
        
        // Add click handlers - toggle expand/collapse dan load details
        container.querySelectorAll('.sidebar-list-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // Prevent event bubbling untuk icon expand
                if (e.target.classList.contains('list-item-expand-icon')) {
                    e.stopPropagation();
                }
                
                const cctvId = this.dataset.id;
                const cctvData = data.find(c => c.id == cctvId);
                
                // Toggle expanded state
                const isExpanded = this.classList.contains('expanded');
                
                if (isExpanded) {
                    // Collapse
                    this.classList.remove('expanded');
                } else {
                    // Expand - load details
                    this.classList.add('expanded');
                    loadCctvDetails(cctvId, this);
                    
                    // Jika CCTV punya koordinat, zoom ke lokasi
                    if (cctvData) {
                        const hasLocation = cctvData.has_location !== false && cctvData.location && Array.isArray(cctvData.location) && cctvData.location.length === 2;
                        if (hasLocation) {
                            highlightAndZoomToLocation(cctvData.location, 'cctv', cctvData);
                        }
                    }
                }
                
                // Highlight active item
                document.querySelectorAll('.sidebar-list-item').forEach(i => {
                    if (i !== this) i.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    }
    
    // Load CCTV details from API
    function loadCctvDetails(cctvId, itemElement) {
        const detailSection = itemElement.querySelector('.cctv-detail-section');
        if (!detailSection) return;
        
        // Check if already loaded
        if (detailSection.dataset.loaded === 'true') {
            return;
        }
        
        // Show loading
        detailSection.innerHTML = `
            <div class="cctv-detail-loading">
                <i class="material-icons-outlined" style="font-size: 24px; margin-bottom: 8px; opacity: 0.7;">hourglass_empty</i>
                <div style="margin-top: 8px;">Memuat detail...</div>
            </div>
        `;
        
        // Fetch details
        const detailsUrl = `{{ url('cctv-data') }}/${cctvId}/details`;
        fetch(detailsUrl)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    detailSection.dataset.loaded = 'true';
                    renderCctvDetails(result.data, detailSection);
                } else {
                    detailSection.innerHTML = `
                        <div class="cctv-detail-error">
                            <i class="material-icons-outlined" style="font-size: 18px;">error_outline</i>
                            <span>Error: ${escapeHtml(result.error || 'Gagal memuat detail')}</span>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading CCTV details:', error);
                detailSection.innerHTML = `
                    <div class="cctv-detail-error">
                        <i class="material-icons-outlined" style="font-size: 18px;">error_outline</i>
                        <span>Error: Gagal memuat detail CCTV</span>
                    </div>
                `;
            });
    }
    
    // Render CCTV details
    function renderCctvDetails(data, container) {
        const { coverages, hazard_stats, pja_list, week_range } = data;
        
        let html = '';
        
        // Coverage Lokasi Section
        html += '<div class="cctv-detail-group">';
        html += '<div class="cctv-detail-group-title"><i class="material-icons-outlined">location_on</i> <span>Coverage Lokasi</span></div>';
        if (coverages && coverages.length > 0) {
            coverages.forEach(coverage => {
                html += `
                    <div class="cctv-coverage-item">
                        <div class="cctv-coverage-lokasi">${escapeHtml(coverage.coverage_lokasi || '-')}</div>
                        <div class="cctv-coverage-detail">${escapeHtml(coverage.coverage_detail_lokasi || '-')}</div>
                    </div>
                `;
            });
        } else {
            html += '<div class="cctv-no-data">Tidak ada data coverage</div>';
        }
        html += '</div>';
        
        // Hazard Inspection Statistics Section
        html += '<div class="cctv-detail-group">';
        html += '<div class="cctv-detail-group-title"><i class="material-icons-outlined">warning</i> <span>Inspeksi Hazard (Minggu Ini)</span></div>';
        if (week_range) {
            html += `<div style="font-size: 11px; color: #6b7280; margin-bottom: 10px; padding: 6px 10px; background: #f3f4f6; border-radius: 4px; display: inline-block;">📅 ${week_range.start} - ${week_range.end}</div>`;
        }
        if (hazard_stats && hazard_stats.length > 0) {
            hazard_stats.forEach(stat => {
                html += `
                    <div class="cctv-hazard-stat">
                        <div class="cctv-hazard-stat-header">${escapeHtml(stat.detail_lokasi || '-')}</div>
                        <div class="cctv-hazard-stat-count">Total: ${stat.total_count} inspeksi</div>
                    </div>
                `;
            });
        } else {
            html += '<div class="cctv-no-data">Tidak ada inspeksi hazard minggu ini</div>';
        }
        html += '</div>';
        
        // PJA Section
        html += '<div class="cctv-detail-group">';
        html += '<div class="cctv-detail-group-title"><i class="material-icons-outlined">person</i> <span>PJA Lokasi</span></div>';
        if (pja_list && Object.keys(pja_list).length > 0) {
            Object.keys(pja_list).forEach(detailLokasi => {
                const pjas = pja_list[detailLokasi];
                pjas.forEach(pja => {
                    html += `
                        <div class="cctv-pja-item">
                            <div class="cctv-pja-name">${escapeHtml(pja.nama_pja || '-')}</div>
                            <div class="cctv-pja-info">
                                ${escapeHtml(pja.employee_name || '-')} (${escapeHtml(pja.kode_sid || '-')})
                                ${pja.employee_email ? `<br>📧 ${escapeHtml(pja.employee_email)}` : ''}
                            </div>
                        </div>
                    `;
                });
            });
        } else {
            html += '<div class="cctv-no-data">Tidak ada data PJA</div>';
        }
        html += '</div>';
        
        container.innerHTML = html;
    }
    
    // Load hazard status for multiple CCTV
    function loadCctvHazardStatus(cctvIds) {
        if (!cctvIds || cctvIds.length === 0) return;
        
        const statusUrl = `{{ url('cctv-data/hazard-status') }}?ids=${cctvIds.join(',')}`;
        
        fetch(statusUrl)
            .then(response => response.json())
            .then(result => {
                if (result.success && result.data) {
                    // Update status for each CCTV item
                    Object.keys(result.data).forEach(cctvId => {
                        const status = result.data[cctvId];
                        const item = document.querySelector(`.sidebar-list-item[data-id="${cctvId}"]`);
                        if (item) {
                            const statusIcon = item.querySelector('.cctv-hazard-status-icon');
                            const hasHazard = status.has_hazard_inspection;
                            
                            // Update data attribute
                            item.setAttribute('data-hazard-status', hasHazard ? 'has' : 'no');
                            
                            // Update classes
                            item.classList.remove('no-hazard-inspection', 'has-hazard-inspection');
                            if (hasHazard) {
                                item.classList.add('has-hazard-inspection');
                            } else {
                                item.classList.add('no-hazard-inspection');
                            }
                            
                            // Update icon
                            if (statusIcon) {
                                statusIcon.classList.remove('loading', 'has-hazard', 'no-hazard');
                                if (hasHazard) {
                                    statusIcon.classList.add('has-hazard');
                                    statusIcon.innerHTML = '<i class="material-icons-outlined" style="font-size: 14px;">check_circle</i>';
                                    statusIcon.title = `Ada ${status.total_count} inspeksi hazard minggu ini`;
                                } else {
                                    statusIcon.classList.add('no-hazard');
                                    statusIcon.innerHTML = '<i class="material-icons-outlined" style="font-size: 14px;">warning</i>';
                                    statusIcon.title = 'Belum ada inspeksi hazard minggu ini';
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error loading CCTV hazard status:', error);
                // Update all items to show error state
                cctvIds.forEach(cctvId => {
                    const item = document.querySelector(`.sidebar-list-item[data-id="${cctvId}"]`);
                    if (item) {
                        const statusIcon = item.querySelector('.cctv-hazard-status-icon');
                        if (statusIcon) {
                            statusIcon.classList.remove('loading', 'has-hazard', 'no-hazard');
                            statusIcon.classList.add('no-hazard');
                            statusIcon.innerHTML = '<i class="material-icons-outlined" style="font-size: 14px;">error_outline</i>';
                            statusIcon.title = 'Error memuat status';
                        }
                    }
                });
            });
    }
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Render SAP list
    function renderSapList(data) {
        const container = document.getElementById('sapList');
        if (!container) return;
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="material-icons-outlined">assignment</i>
                    <p>Tidak ada data SAP</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = data.map((sap, index) => {
            const name = sap.jenis_laporan || sap.aktivitas_pekerjaan || sap.task_number || `SAP ${sap.id}`;
            const taskNumber = sap.task_number || '';
            const lokasi = sap.lokasi || sap.detail_lokasi || '';
            const firstLetter = getFirstLetter(name);
            const avatarColor = '#3b82f6'; // Blue untuk SAP
            const timestamp = formatTimestamp(sap.tanggal_pelaporan || sap.detected_at);
            
            return `
                <div class="sidebar-list-item" data-type="sap" data-id="${sap.id}" data-index="${index}">
                    <div class="list-item-avatar" style="background-color: ${avatarColor};">
                        ${firstLetter}
                    </div>
                    <div class="list-item-content">
                        <div class="list-item-title">${name}</div>
                        <div class="list-item-subtitle">${taskNumber ? `Task: ${taskNumber}` : ''}${lokasi ? ` - ${lokasi}` : ''}</div>
                        ${timestamp ? `<div class="list-item-time">${timestamp}</div>` : ''}
                    </div>
                </div>
            `;
        }).join('');
        
        // Add click handlers
        container.querySelectorAll('.sidebar-list-item').forEach(item => {
            item.addEventListener('click', function() {
                const sapId = this.dataset.id;
                const sapData = data.find(s => s.id == sapId);
                if (sapData) {
                    // Highlight item di sidebar
                    document.querySelectorAll('.sidebar-list-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Jika punya koordinat, zoom ke lokasi
                    if (sapData.location && sapData.location.lat && sapData.location.lng) {
                        highlightAndZoomToLocation(sapData.location, 'sap', sapData);
                    }
                    
                    // Buka modal detail SAP
                    if (sapData.task_number) {
                        openSapDetailModal(sapData.task_number);
                    } else {
                        // Jika tidak ada task_number, gunakan data langsung
                        const modal = new bootstrap.Modal(document.getElementById('sapDetailModal'));
                        modal.show();
                        populateSapDetailModal(sapData);
                    }
                }
            });
        });
    }
    
    // Render Insiden list
    function renderInsidenList(data) {
        const container = document.getElementById('insidenList');
        if (!container) return;
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="material-icons-outlined">report_problem</i>
                    <p>Tidak ada data Insiden</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = data.map((insiden, index) => {
            const name = insiden.no_kecelakaan || `Insiden ${index + 1}`;
            const lokasi = insiden.lokasi || '';
            const firstLetter = getFirstLetter(name);
            const avatarColor = '#f97316';
            const timestamp = formatTimestamp(insiden.tanggal);
            
            return `
                <div class="sidebar-list-item" data-type="insiden" data-id="${insiden.no_kecelakaan}" data-index="${index}">
                    <div class="list-item-avatar" style="background-color: ${avatarColor};">
                        ${firstLetter}
                    </div>
                    <div class="list-item-content">
                        <div class="list-item-title">${name}</div>
                        <div class="list-item-subtitle">${lokasi || 'Lokasi tidak diketahui'}</div>
                        ${timestamp ? `<div class="list-item-time">${timestamp}</div>` : ''}
                    </div>
                </div>
            `;
        }).join('');
        
        // Add click handlers
        container.querySelectorAll('.sidebar-list-item').forEach(item => {
            item.addEventListener('click', function() {
                const insidenId = this.dataset.id;
                const insidenData = data.find(i => i.no_kecelakaan == insidenId);
                if (insidenData && insidenData.latitude && insidenData.longitude) {
                    highlightAndZoomToLocation([insidenData.longitude, insidenData.latitude], 'insiden', insidenData);
                }
            });
        });
    }
    
    // Render Unit list
    function renderUnitList(data) {
        const container = document.getElementById('unitList');
        if (!container) {
            console.warn('unitList container not found');
            return;
        }
        
        console.log('Rendering unit list, data count:', data ? data.length : 0, 'data:', data);
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="material-icons-outlined">directions_car</i>
                    <p>Tidak ada data Unit</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = data.map((unit, index) => {
            const name = unit.unit_id || unit.unit_name || unit.vehicle_name || unit.vehicle_number || unit.integration_id || `Unit ${index + 1}`;
            const id = unit.integration_id || unit.unit_id || '';
            const vehicleNumber = unit.vehicle_number || '';
            const vehicleType = unit.vehicle_type || '';
            const firstLetter = getFirstLetter(name);
            const avatarColor = getAvatarColor(firstLetter);
            const timestamp = formatTimestamp(unit.timestamp || unit.updated_at || unit.last_update);
            
            return `
                <div class="sidebar-list-item" data-type="unit" data-id="${id || unit.unit_id || unit.integration_id}" data-index="${index}">
                    <div class="list-item-avatar" style="background-color: ${avatarColor};">
                        ${firstLetter}
                    </div>
                    <div class="list-item-content">
                        <div class="list-item-title">${name}</div>
                        <div class="list-item-subtitle">
                            ${vehicleNumber ? `No: ${vehicleNumber}` : ''} 
                            ${vehicleType ? `- ${vehicleType}` : ''}
                            ${id ? ` (ID: ${id})` : ''}
                        </div>
                        ${timestamp ? `<div class="list-item-time">${timestamp}</div>` : ''}
                    </div>
                </div>
            `;
        }).join('');
        
        // Add click handlers
        container.querySelectorAll('.sidebar-list-item').forEach(item => {
            item.addEventListener('click', function() {
                const unitId = this.dataset.id;
                const unitData = data.find(u => {
                    const uId = u.integration_id || u.unit_id || u.id;
                    return uId == unitId;
                });
                if (unitData) {
                    // Highlight item di sidebar
                    document.querySelectorAll('.sidebar-list-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Jika punya koordinat, zoom ke lokasi
                    if (unitData.latitude && unitData.longitude && unitData.latitude != 0 && unitData.longitude != 0) {
                        highlightAndZoomToLocation({ lat: unitData.latitude, lng: unitData.longitude }, 'unit', unitData);
                    }
                }
            });
        });
    }
    
    // Initialize Control Room data - group CCTV by control_room
    function initializeControlRoomData() {
        if (!cctvLocations || cctvLocations.length === 0) {
            filteredSidebarData.controlroom = [];
            originalControlRoomData = [];
            return;
        }
        
        // Group CCTV by control_room (filter out empty/null control_room)
        const controlRoomMap = {};
        
        cctvLocations.forEach(cctv => {
            const controlRoom = (cctv.control_room && cctv.control_room.trim() !== '') 
                ? cctv.control_room.trim() 
                : null;
            
            // Skip if control_room is null or empty
            if (!controlRoom) return;
            
            if (!controlRoomMap[controlRoom]) {
                controlRoomMap[controlRoom] = {
                    name: controlRoom,
                    cctv_list: []
                };
            }
            
            controlRoomMap[controlRoom].cctv_list.push(cctv);
        });
        
        // Convert to array and sort by name
        const controlRoomData = Object.values(controlRoomMap)
            .sort((a, b) => a.name.localeCompare(b.name));
        
        // Store original data for filtering
        originalControlRoomData = JSON.parse(JSON.stringify(controlRoomData));
        filteredSidebarData.controlroom = controlRoomData;
    }
    
    // Render Control Room list
    function renderControlRoomList(data) {
        const container = document.getElementById('controlroomList');
        if (!container) return;
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="material-icons-outlined">meeting_room</i>
                    <p>Tidak ada data Control Room</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = data.map((controlRoom, index) => {
            const name = controlRoom.name || 'Unknown';
            const cctvCount = controlRoom.cctv_list ? controlRoom.cctv_list.length : 0;
            const firstLetter = getFirstLetter(name);
            const avatarColor = getAvatarColor(firstLetter);
            
            // Build CCTV list HTML
            const cctvListHtml = controlRoom.cctv_list ? controlRoom.cctv_list.map((cctv, cctvIndex) => {
                const cctvName = cctv.name || cctv.nama_cctv || cctv.no_cctv || `CCTV ${cctv.id}`;
                const cctvId = cctv.no_cctv || cctv.nomor_cctv || cctv.id || '';
                const cctvKondisi = cctv.kondisi || cctv.status || '';
                const cctvLinkAkses = cctv.link_akses || cctv.externalUrl || '';
                const cctvLokasi = cctv.lokasi_pemasangan || cctv.coverage_detail_lokasi || '';
                const kategoriArea = cctv.kategori_area_tercapture || '';
                
                let kondisiBadge = '';
                if (cctvKondisi === 'Baik') {
                    kondisiBadge = '<span class="badge bg-success rounded-pill ms-2" style="font-size: 10px; padding: 2px 8px; font-weight: 500;">Baik</span>';
                } else if (cctvKondisi === 'Rusak') {
                    kondisiBadge = '<span class="badge bg-danger rounded-pill ms-2" style="font-size: 10px; padding: 2px 8px; font-weight: 500;">Rusak</span>';
                }
                
                return `
                    <div class="controlroom-cctv-item" data-cctv-id="${cctv.id}" data-index="${cctvIndex}">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;">
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 4px; margin-bottom: 4px;">
                                    <span style="font-size: 13px; font-weight: 500; color: #111827; line-height: 1.4;">
                                        ${escapeHtml(cctvName)}
                                    </span>
                                    ${kondisiBadge}
                                </div>
                                ${cctvId ? `
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 2px; display: flex; align-items: center; gap: 6px;">
                                        <span>${escapeHtml(cctvId)}</span>
                                        ${cctvLinkAkses ? `
                                            <button class="btn btn-link p-0" style="color: #3b82f6; text-decoration: none; min-width: auto; padding: 0; line-height: 1; height: auto;" 
                                                    onclick="event.stopPropagation(); window.open('${cctvLinkAkses}', '_blank');" 
                                                    title="Buka Link Akses">
                                                <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">open_in_new</i>
                                            </button>
                                        ` : ''}
                                    </div>
                                ` : ''}
                                ${cctvLokasi ? `
                                    <div style="font-size: 11px; color: #9ca3af; line-height: 1.4; margin-top: 2px;">
                                        ${escapeHtml(cctvLokasi)}
                                    </div>
                                ` : ''}
                                ${kategoriArea ? `
                                    <div style="font-size: 10px; color: #d1d5db; margin-top: 4px; font-style: italic;">
                                        ${escapeHtml(kategoriArea)}
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('') : '';
            
            return `
                <div class="sidebar-list-item controlroom-item" data-type="controlroom" data-controlroom="${escapeHtml(name)}" data-index="${index}">
                    <div class="sidebar-list-item-header">
                        <div class="list-item-avatar" style="background-color: ${avatarColor};">
                            ${firstLetter}
                        </div>
                        <div class="list-item-content" style="flex: 1;">
                            <div class="list-item-title">${escapeHtml(name)}</div>
                            <div class="list-item-subtitle">
                                <span style="font-weight: 500; color: #3b82f6;">${cctvCount}</span> CCTV
                            </div>
                        </div>
                        <i class="material-icons-outlined list-item-expand-icon">expand_more</i>
                    </div>
                    <div class="controlroom-cctv-list">
                        ${cctvListHtml || '<div style="padding: 16px; text-align: center; color: #9ca3af; font-size: 12px;">Tidak ada CCTV</div>'}
                    </div>
                </div>
            `;
        }).join('');
        
        // Add click handlers for expand/collapse
        container.querySelectorAll('.controlroom-item').forEach(item => {
            const header = item.querySelector('.sidebar-list-item-header');
            const cctvList = item.querySelector('.controlroom-cctv-list');
            const expandIcon = item.querySelector('.list-item-expand-icon');
            
            header.addEventListener('click', function(e) {
                e.stopPropagation();
                
                const isExpanded = item.classList.contains('expanded');
                
                if (isExpanded) {
                    // Collapse
                    item.classList.remove('expanded');
                } else {
                    // Expand
                    item.classList.add('expanded');
                }
            });
            
            // Add click handlers for CCTV items
            item.querySelectorAll('.controlroom-cctv-item').forEach(cctvItem => {
                cctvItem.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const cctvId = this.dataset.cctvId;
                    const cctvData = cctvLocations.find(c => c.id == cctvId);
                    
                    if (cctvData) {
                        // Remove active class from all items
                        document.querySelectorAll('.controlroom-cctv-item').forEach(i => i.classList.remove('active'));
                        // Add active class to clicked item
                        this.classList.add('active');
                        
                        // Scroll into view
                        this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // Jika CCTV punya koordinat, zoom ke lokasi
                        if (cctvData.has_location !== false && cctvData.location && Array.isArray(cctvData.location) && cctvData.location.length === 2) {
                            highlightAndZoomToLocation(cctvData.location, 'cctv', cctvData);
                        }
                    }
                });
            });
        });
    }
    
    // Check if user is admin (from backend)
    const isUserAdmin = @json(auth()->user() && (method_exists(auth()->user(), 'isAdmin') ? auth()->user()->isAdmin() : (isset(auth()->user()->role) && (auth()->user()->role === 'admin' || auth()->user()->role === 'administrator'))));
    
    // Load PJA data from API
    function loadPjaData() {
        const container = document.getElementById('pjaList');
        if (!container) return;
        
        // Show loading state
        container.innerHTML = `
            <div class="empty-state">
                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p style="margin-top: 16px;">Memuat data PJA...</p>
            </div>
        `;
        
        // Update tab count to show loading
        const pjaTabCount = document.getElementById('pjaTabCount');
        if (pjaTabCount) pjaTabCount.textContent = '...';
        
        fetch('{{ route("maps.api.pja-data") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    originalPjaData = data.data;
                    filteredSidebarData.pja = data.data;
                    updateTabCounts();
                    renderPjaList(filteredSidebarData.pja);
                } else {
                    filteredSidebarData.pja = [];
                    updateTabCounts();
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="material-icons-outlined">description</i>
                            <p>Tidak ada data PJA</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading PJA data:', error);
                filteredSidebarData.pja = [];
                updateTabCounts();
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="material-icons-outlined">error_outline</i>
                        <p>Gagal memuat data PJA</p>
                        <small style="color: #9ca3af;">${error.message}</small>
                    </div>
                `;
            });
    }
    
    // Render PJA list
    function renderPjaList(data) {
        const container = document.getElementById('pjaList');
        if (!container) return;
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="material-icons-outlined">description</i>
                    <p>Tidak ada data PJA</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = data.map((pja, index) => {
            const namaPja = pja.nama_pja || `PJA ${pja.pja_id || index + 1}`;
            const site = pja.site || '';
            const lokasi = pja.lokasi || '';
            const detailLokasi = pja.detail_lokasi || '';
            const pjaType = pja.pja_type_name || '';
            const pjaCategory = pja.pja_category_name || '';
            const pjaLayer = pja.pja_layer || '';
            const employeeName = pja.employee_name || '';
            const nik = pja.nik || '';
            const kodeSid = pja.kode_sid || '';
            const kategoriPja = pja.kategori_pja || '';
            const firstLetter = getFirstLetter(namaPja);
            const avatarColor = '#8b5cf6'; // Purple untuk PJA
            
            return `
                <div class="sidebar-list-item" data-type="pja" data-id="${pja.pja_id || index}" data-index="${index}">
                    <div class="list-item-avatar" style="background-color: ${avatarColor};">
                        ${firstLetter}
                    </div>
                    <div class="list-item-content">
                        <div class="list-item-title">${namaPja}</div>
                        <div class="list-item-subtitle">
                            ${site ? `Site: ${site}` : ''} 
                            ${lokasi ? `- ${lokasi}` : ''}
                            ${detailLokasi ? ` (${detailLokasi})` : ''}
                        </div>
                        <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                            ${pjaType ? `<span>Type: ${pjaType}</span>` : ''}
                            ${pjaCategory ? ` | Category: ${pjaCategory}` : ''}
                            ${pjaLayer ? ` | Layer: ${pjaLayer}` : ''}
                        </div>
                        ${employeeName ? `
                            <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                ${employeeName} ${nik ? `(${nik})` : ''} ${kodeSid ? `- ${kodeSid}` : ''}
                            </div>
                        ` : ''}
                        ${kategoriPja ? `
                            <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                Kategori: ${kategoriPja}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');
        
        // Add click handlers
        container.querySelectorAll('.sidebar-list-item').forEach(item => {
            item.addEventListener('click', function() {
                const pjaId = this.dataset.id;
                const pjaData = data.find(p => (p.pja_id || p.id) == pjaId);
                if (pjaData) {
                    // Highlight item di sidebar
                    document.querySelectorAll('.sidebar-list-item').forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Log PJA data for debugging (can be extended to show modal or zoom to location)
                    console.log('PJA Data:', pjaData);
                }
            });
        });
    }
    
    // Load evaluation summary
    function loadEvaluationSummary(type, idLokasi, lokasiName, nomorCctv, cctvName) {
        // Switch to evaluasi tab
        currentSidebarTab = 'evaluasi';
        document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
        const evaluasiTab = document.querySelector('.sidebar-tab[data-tab="evaluasi"]');
        if (evaluasiTab) {
            evaluasiTab.classList.add('active');
        }
        renderSidebarTab('evaluasi');
        
        // Show loading state
        const evaluasiContent = document.getElementById('evaluasiContent');
        if (evaluasiContent) {
            evaluasiContent.innerHTML = `
                <div style="text-align: center; padding: 40px 20px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p style="margin-top: 16px; color: #6b7280;">Memuat data evaluasi...</p>
                </div>
            `;
        }
        
        // Prepare request data
        const requestData = {
            type: type,
            id_lokasi: idLokasi || null,
            lokasi_name: lokasiName || null,
            nomor_cctv: nomorCctv || null,
            cctv_name: cctvName || null
        };
        
        // Make API call
        fetch('{{ route("maps.api.evaluation-summary") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderEvaluationSummary(data.data);
            } else {
                throw new Error(data.message || 'Error loading evaluation summary');
            }
        })
        .catch(error => {
            console.error('Error loading evaluation summary:', error);
            if (evaluasiContent) {
                evaluasiContent.innerHTML = `
                    <div style="text-align: center; padding: 40px 20px; color: #ef4444;">
                        <i class="material-icons-outlined" style="font-size: 48px; margin-bottom: 12px;">error_outline</i>
                        <p style="margin: 0; font-size: 14px;">Error: ${error.message || 'Gagal memuat data evaluasi'}</p>
                    </div>
                `;
            }
        });
    }
    
    // Render evaluation summary
    function renderEvaluationSummary(summary) {
        const evaluasiContent = document.getElementById('evaluasiContent');
        if (!evaluasiContent) return;
        
        const areaName = summary.area_name || 'N/A';
        const areaType = summary.area_type === 'area_kerja' ? 'Area Kerja' : summary.area_type === 'area_cctv' ? 'Area CCTV' : 'Area';
        const cctvList = summary.cctv_list || [];
        const today = new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        
        // Build CCTV list HTML
        let cctvListHtml = '';
        if (cctvList.length > 0) {
            cctvListHtml = cctvList.map(cctv => `
                <div style="padding: 10px; background: #f9fafb; border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #3b82f6;">
                    <div style="font-weight: 600; font-size: 13px; color: #111827; margin-bottom: 4px;">
                        ${cctv.nama_cctv || cctv.no_cctv || 'CCTV'}
                    </div>
                    <div style="font-size: 11px; color: #6b7280;">
                        ${cctv.no_cctv ? `No: ${cctv.no_cctv}` : ''} ${cctv.lokasi ? `| ${cctv.lokasi}` : ''}
                    </div>
                </div>
            `).join('');
        } else {
            cctvListHtml = '<p style="margin: 0; font-size: 12px; color: #9ca3af; text-align: center; padding: 20px;">Tidak ada CCTV tercover</p>';
        }
        
        evaluasiContent.innerHTML = `
            <div style="padding: 16px;">
                <div style="margin-bottom: 20px;">
                    <h6 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #111827;">${areaType}</h6>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">${areaName}</p>
                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #9ca3af;">${today}</p>
                </div>
                
                <!-- CCTV Tercover -->
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <i class="material-icons-outlined" style="font-size: 20px; color: #6366f1;">videocam</i>
                        <h6 style="margin: 0; font-size: 14px; font-weight: 600; color: #111827;">CCTV Tercover (${cctvList.length})</h6>
                    </div>
                    <div style="max-height: 200px; overflow-y: auto;">
                        ${cctvListHtml}
                    </div>
                </div>
                
                <!-- Summary Cards -->
                <div style="display: grid; grid-template-columns: 1fr; gap: 12px; margin-bottom: 20px;">
                    <!-- Inspeksi Hari Ini -->
                    <div style="background: #dbeafe; border: 1px solid #93c5fd; border-radius: 8px; padding: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="material-icons-outlined" style="font-size: 24px; color: #3b82f6;">assignment</i>
                                <span style="font-size: 14px; font-weight: 600; color: #1e40af;">Inspeksi</span>
                            </div>
                            <span style="font-size: 24px; font-weight: 700; color: #1e40af;">${summary.inspeksi_count || 0}</span>
                        </div>
                        <p style="margin: 0; font-size: 12px; color: #1e3a8a;">Jumlah inspeksi hari ini</p>
                    </div>
                    
                    <!-- Hazard Hari Ini -->
                    <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="material-icons-outlined" style="font-size: 24px; color: #f59e0b;">warning</i>
                                <span style="font-size: 14px; font-weight: 600; color: #92400e;">Hazard</span>
                            </div>
                            <span style="font-size: 24px; font-weight: 700; color: #92400e;">${summary.hazard_count || 0}</span>
                        </div>
                        <p style="margin: 0; font-size: 12px; color: #78350f;">Jumlah hazard hari ini</p>
                    </div>
                    
                    <!-- Coaching Hari Ini -->
                    <div style="background: #e0e7ff; border: 1px solid #a5b4fc; border-radius: 8px; padding: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="material-icons-outlined" style="font-size: 24px; color: #6366f1;">school</i>
                                <span style="font-size: 14px; font-weight: 600; color: #4338ca;">Coaching</span>
                            </div>
                            <span style="font-size: 24px; font-weight: 700; color: #4338ca;">${summary.coaching_count || 0}</span>
                        </div>
                        <p style="margin: 0; font-size: 12px; color: #3730a3;">Jumlah coaching hari ini</p>
                    </div>
                    
                    <!-- Observasi Hari Ini -->
                    <div style="background: #d1fae5; border: 1px solid #6ee7b7; border-radius: 8px; padding: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="material-icons-outlined" style="font-size: 24px; color: #10b981;">visibility</i>
                                <span style="font-size: 14px; font-weight: 600; color: #065f46;">Observasi</span>
                            </div>
                            <span style="font-size: 24px; font-weight: 700; color: #065f46;">${summary.observasi_count || 0}</span>
                        </div>
                        <p style="margin: 0; font-size: 12px; color: #047857;">Jumlah observasi hari ini</p>
                    </div>
                    
                    <!-- Observasi Area Kritis Hari Ini -->
                    <div style="background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; padding: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="material-icons-outlined" style="font-size: 24px; color: #ef4444;">dangerous</i>
                                <span style="font-size: 14px; font-weight: 600; color: #991b1b;">Observasi Area Kritis</span>
                            </div>
                            <span style="font-size: 24px; font-weight: 700; color: #991b1b;">${summary.observasi_area_kritis_count || 0}</span>
                        </div>
                        <p style="margin: 0; font-size: 12px; color: #7f1d1d;">Jumlah observasi area kritis hari ini</p>
                    </div>
                </div>
                
                <div style="padding-top: 16px; border-top: 1px solid #e5e7eb;">
                    <p style="margin: 0; font-size: 12px; color: #6b7280; text-align: center;">
                        <i class="material-icons-outlined" style="font-size: 14px; vertical-align: middle;">info_outline</i>
                        Data dihitung berdasarkan area yang dipilih untuk hari ini
                    </p>
                </div>
            </div>
        `;
    }
    
    // Render sidebar tab content
    function renderSidebarTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Show selected tab content
        const tabContent = document.getElementById(`tabContent${tabName.charAt(0).toUpperCase() + tabName.slice(1)}`);
        if (tabContent) {
            tabContent.classList.add('active');
        }
        
        // Render appropriate list
        switch(tabName) {
            case 'cctv':
                renderCctvList(filteredSidebarData.cctv);
                break;
            case 'sap':
                renderSapList(filteredSidebarData.sap);
                break;
            case 'hazard':
                renderSapList(filteredSidebarData.sap); // Alias untuk kompatibilitas
                break;
            case 'insiden':
                renderInsidenList(filteredSidebarData.insiden);
                break;
            case 'unit':
                renderUnitList(filteredSidebarData.unit);
                break;
            case 'gps':
                // GPS functionality removed - no longer used
                break;
            case 'controlroom':
                renderControlRoomList(filteredSidebarData.controlroom);
                break;
            case 'pja':
                if (filteredSidebarData.pja.length === 0) {
                    loadPjaData();
                } else {
                    renderPjaList(filteredSidebarData.pja);
                }
                break;
            case 'evaluasi':
                // Evaluasi content will be rendered by loadEvaluationSummary
                break;
        }
    }
    
    // Highlight and zoom to location on map
    function highlightAndZoomToLocation(location, type, data) {
        if (!location || !map) return;
        
        let coordinates;
        if (Array.isArray(location)) {
            coordinates = location.length === 2 ? ol.proj.fromLonLat(location) : location;
        } else if (location.lat && location.lng) {
            coordinates = ol.proj.fromLonLat([location.lng, location.lat]);
        } else {
            return;
        }
        
        // Zoom to location
        map.getView().animate({
            center: coordinates,
            zoom: 18,
            duration: 1000
        });
        
        // Highlight item in sidebar
        document.querySelectorAll('.sidebar-list-item').forEach(item => {
            item.classList.remove('active');
        });
        
        const activeItem = document.querySelector(`.sidebar-list-item[data-type="${type}"][data-id="${data.id || data.user_id || data.no_kecelakaan || data.integration_id || data.unit_id}"]`);
        if (activeItem) {
            activeItem.classList.add('active');
            activeItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    // Filter sidebar data
    // Data CCTV diambil langsung dari database (cctvLocations), bukan dari WMS atau GeoJSON
    function filterSidebarData(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        
        if (!term) {
            // Reset ke data asli dari database
            filteredSidebarData.cctv = [...(cctvLocations || [])];
            filteredSidebarData.sap = [...(sapData || [])];
            filteredSidebarData.insiden = [...(insidenDataset || [])];
            filteredSidebarData.unit = [...(unitVehicles || [])];
            // GPS data removed - no longer used
            // Reset Control Room data ke original jika sudah ada
            if (originalControlRoomData && originalControlRoomData.length > 0) {
                filteredSidebarData.controlroom = JSON.parse(JSON.stringify(originalControlRoomData));
            }
            // Reset PJA data ke original jika sudah ada
            if (originalPjaData && originalPjaData.length > 0) {
                filteredSidebarData.pja = [...originalPjaData];
            }
        } else {
            // Filter data CCTV dari database berdasarkan search term
            filteredSidebarData.cctv = (cctvLocations || []).filter(cctv => {
                const name = (cctv.name || cctv.nama_cctv || cctv.no_cctv || '').toLowerCase();
                const noCctv = (cctv.no_cctv || cctv.nomor_cctv || '').toLowerCase();
                const id = String(cctv.id || '').toLowerCase();
                const site = (cctv.site || '').toLowerCase();
                const perusahaan = (cctv.perusahaan || '').toLowerCase();
                return name.includes(term) || noCctv.includes(term) || id.includes(term) || 
                       site.includes(term) || perusahaan.includes(term);
            });
            
            filteredSidebarData.sap = (sapData || []).filter(sap => {
                const jenisLaporan = (sap.jenis_laporan || '').toLowerCase();
                const aktivitas = (sap.aktivitas_pekerjaan || '').toLowerCase();
                const lokasi = (sap.lokasi || sap.detail_lokasi || '').toLowerCase();
                const taskNumber = String(sap.task_number || '').toLowerCase();
                const pelapor = (sap.pelapor || sap.nama_pelapor || '').toLowerCase();
                return jenisLaporan.includes(term) || aktivitas.includes(term) || 
                       lokasi.includes(term) || taskNumber.includes(term) || pelapor.includes(term);
            });
            
            filteredSidebarData.insiden = (insidenDataset || []).filter(insiden => {
                const noKecelakaan = (insiden.no_kecelakaan || '').toLowerCase();
                const lokasi = (insiden.lokasi || '').toLowerCase();
                return noKecelakaan.includes(term) || lokasi.includes(term);
            });
            
            filteredSidebarData.unit = (unitVehicles || []).filter(unit => {
                const unitId = (unit.unit_id || unit.unit_name || unit.integration_id || '').toLowerCase();
                const id = String(unit.integration_id || unit.unit_id || '').toLowerCase();
                return unitId.includes(term) || id.includes(term);
            });
            
            // Filter Control Room data berdasarkan search term
            if (filteredSidebarData.controlroom && filteredSidebarData.controlroom.length > 0) {
                filteredSidebarData.controlroom = filteredSidebarData.controlroom.filter(controlRoom => {
                    const name = (controlRoom.name || '').toLowerCase();
                    // Filter juga berdasarkan nama CCTV di dalam control room
                    const hasMatchingCctv = controlRoom.cctv_list && controlRoom.cctv_list.some(cctv => {
                        const cctvName = ((cctv.name || cctv.nama_cctv || cctv.no_cctv || '') + ' ' + (cctv.lokasi_pemasangan || '')).toLowerCase();
                        return cctvName.includes(term);
                    });
                    return name.includes(term) || hasMatchingCctv;
                });
            }
            
            // GPS data filtering removed - no longer used
            
            // Filter PJA data berdasarkan search term
            if (originalPjaData && originalPjaData.length > 0) {
                filteredSidebarData.pja = originalPjaData.filter(pja => {
                    const namaPja = (pja.nama_pja || '').toLowerCase();
                    const site = (pja.site || '').toLowerCase();
                    const lokasi = (pja.lokasi || '').toLowerCase();
                    const detailLokasi = (pja.detail_lokasi || '').toLowerCase();
                    const employeeName = (pja.employee_name || '').toLowerCase();
                    const nik = (pja.nik || '').toLowerCase();
                    const kodeSid = (pja.kode_sid || '').toLowerCase();
                    const pjaType = (pja.pja_type_name || '').toLowerCase();
                    const pjaCategory = (pja.pja_category_name || '').toLowerCase();
                    const kategoriPja = (pja.kategori_pja || '').toLowerCase();
                    return namaPja.includes(term) || site.includes(term) || lokasi.includes(term) || 
                           detailLokasi.includes(term) || employeeName.includes(term) || nik.includes(term) ||
                           kodeSid.includes(term) || pjaType.includes(term) || pjaCategory.includes(term) ||
                           kategoriPja.includes(term);
                });
            }
        }
        
        updateTabCounts();
        renderSidebarTab(currentSidebarTab);
    }
    
    // Sidebar event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize icon toolbar - set CCTV as active by default
        const defaultIconBtn = document.querySelector('.icon-toolbar-btn[data-tab="cctv"]');
        if (defaultIconBtn) {
            defaultIconBtn.classList.add('active');
        }
        
        // Toggle sidebar collapse
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mapSidebar = document.getElementById('mapSidebar');
        
        if (sidebarToggle && mapSidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebarCollapsed = !sidebarCollapsed;
                if (sidebarCollapsed) {
                    mapSidebar.classList.add('collapsed');
                } else {
                    mapSidebar.classList.remove('collapsed');
                }
            });
        }
        
        // Icon Toolbar switching - New implementation
        document.querySelectorAll('.icon-toolbar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                // Update active icon toolbar button
                document.querySelectorAll('.icon-toolbar-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Open sidebar if collapsed
                const mapSidebar = document.getElementById('mapSidebar');
                if (mapSidebar && mapSidebar.classList.contains('collapsed')) {
                    mapSidebar.classList.remove('collapsed');
                }
                
                // Render tab content
                currentSidebarTab = tabName;
                renderSidebarTab(tabName);
            });
        });
        
        // Legacy tab switching (for backward compatibility)
        document.querySelectorAll('.sidebar-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                // Update active tab
                document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Sync with icon toolbar
                document.querySelectorAll('.icon-toolbar-btn').forEach(b => b.classList.remove('active'));
                const iconBtn = document.querySelector(`.icon-toolbar-btn[data-tab="${tabName}"]`);
                if (iconBtn) {
                    iconBtn.classList.add('active');
                }
                
                // Render tab content
                currentSidebarTab = tabName;
                renderSidebarTab(tabName);
            });
        });
        
        // Search functionality
        const searchInput = document.getElementById('sidebarSearchInput');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterSidebarData(this.value);
                }, 300);
            });
        }
        
        // Week filter untuk SAP
        const sapWeekFilter = document.getElementById('sapWeekFilter');
        if (sapWeekFilter) {
            // Set default week (minggu ini - Senin sampai Senin)
            const today = new Date();
            const monday = new Date(today);
            monday.setDate(today.getDate() - (today.getDay() === 0 ? 6 : today.getDay() - 1));
            const year = monday.getFullYear();
            const week = getWeekNumber(monday);
            const weekValue = `${year}-W${String(week).padStart(2, '0')}`;
            sapWeekFilter.value = weekValue;
            updateSapWeekRange();
            
            // JANGAN load SAP data untuk week default saat pertama kali load
            // Gunakan hanya SAP hari ini yang sudah di-filter di awal untuk performa
            // User bisa memilih week lain jika ingin melihat data lebih banyak
            console.log('Skipping initial SAP week load. Using only today\'s SAP data for better performance.');
            
            // Set flag bahwa SAP data sudah ready (dari filter hari ini)
            let sapDataLoading = false;
            
            // Initialize sidebar data langsung dengan SAP hari ini
            setTimeout(() => {
                initializeSidebarData();
            }, 100);
            
            sapWeekFilter.addEventListener('change', function() {
                updateSapWeekRange();
                // Reload SAP data berdasarkan week yang dipilih
                loadSapDataByWeek(this.value);
            });
        } else {
            // Jika tidak ada week filter, initialize sidebar data langsung
            setTimeout(() => {
                initializeSidebarData();
            }, 500);
        }
    });
    
    // Helper function untuk mendapatkan week number
    function getWeekNumber(date) {
        const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        const dayNum = d.getUTCDay() || 7;
        d.setUTCDate(d.getUTCDate() + 4 - dayNum);
        const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
        return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
    }
    
    // Update SAP week range display
    function updateSapWeekRange() {
        const sapWeekFilter = document.getElementById('sapWeekFilter');
        const sapWeekRange = document.getElementById('sapWeekRange');
        if (!sapWeekFilter || !sapWeekRange) return;
        
        const weekValue = sapWeekFilter.value;
        if (!weekValue) {
            sapWeekRange.textContent = 'Week: -';
            return;
        }
        
        // Parse week value (format: YYYY-Www)
        const [year, week] = weekValue.split('-W');
        const weekStart = getDateOfISOWeek(parseInt(week), parseInt(year));
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);
        
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const startStr = `${weekStart.getDate()} ${months[weekStart.getMonth()]} ${weekStart.getFullYear()}`;
        const endStr = `${weekEnd.getDate()} ${months[weekEnd.getMonth()]} ${weekEnd.getFullYear()}`;
        
        sapWeekRange.textContent = `Week: ${startStr} - ${endStr}`;
    }
    
    // Helper function untuk mendapatkan tanggal Senin dari week number
    function getDateOfISOWeek(week, year) {
        const simple = new Date(year, 0, 1 + (week - 1) * 7);
        const dow = simple.getDay();
        const ISOweekStart = simple;
        if (dow <= 4) {
            ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
        } else {
            ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
        }
        return ISOweekStart;
    }
    
    // Load SAP data berdasarkan week yang dipilih
    function loadSapDataByWeek(weekValue) {
        return new Promise((resolve, reject) => {
            if (!weekValue) {
                reject('No week value provided');
                return;
            }
            
            const [year, week] = weekValue.split('-W');
            const weekStart = getDateOfISOWeek(parseInt(week), parseInt(year));
            // Set waktu ke 00:00:00 untuk Senin
            weekStart.setHours(0, 0, 0, 0);
            
            // Format: YYYY-MM-DD HH:MM:SS untuk ClickHouse (Senin 00:00:00)
            // Gunakan format lokal untuk menghindari masalah timezone
            const yearStr = weekStart.getFullYear();
            const monthStr = String(weekStart.getMonth() + 1).padStart(2, '0');
            const dayStr = String(weekStart.getDate()).padStart(2, '0');
            const weekStartStr = `${yearStr}-${monthStr}-${dayStr} 00:00:00`;
            
            // Format tanggal untuk display
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            const startStr = `${weekStart.getDate()} ${months[weekStart.getMonth()]} ${weekStart.getFullYear()}`;
            const endStr = `${weekEnd.getDate()} ${months[weekEnd.getMonth()]} ${weekEnd.getFullYear()}`;
            
            console.log('Loading SAP data for week:', weekValue, 'Start:', weekStartStr, 'Date:', weekStart);
            
            // Show SweetAlert loading
            Swal.fire({
                title: 'Memuat Data SAP',
                html: `Memuat data untuk week: <strong>${startStr} - ${endStr}</strong><br><small>Mohon tunggu...</small>`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Show loading indicator
            const sapTabCount = document.getElementById('sapTabCount');
            if (sapTabCount) {
                sapTabCount.textContent = '...';
            }
            
            // Call API untuk mendapatkan SAP data berdasarkan week
            // Tambahkan timeout 60 detik untuk query yang lebih lama
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 detik timeout
            
            fetch(`{{ route('maps.api.filtered-data') }}?week_start=${encodeURIComponent(weekStartStr)}&show_sap=true&show_hazard=true`, {
                signal: controller.signal
            })
                .then(response => {
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data && (data.data.sap || data.data.hazard)) {
                        // Gunakan sap jika ada, jika tidak gunakan hazard (alias)
                        const newSapData = data.data.sap || data.data.hazard || [];
                        
                        // Filter data berdasarkan tanggal_pelaporan untuk memastikan sesuai week
                        const weekEnd = new Date(weekStart);
                        weekEnd.setDate(weekStart.getDate() + 7); // Senin berikutnya
                        weekEnd.setHours(0, 0, 0, 0);
                        
                        const filteredSapData = newSapData.filter(sap => {
                            if (!sap.tanggal_pelaporan && !sap.detected_at) return false;
                            
                            try {
                                const sapDate = new Date(sap.tanggal_pelaporan || sap.detected_at);
                                sapDate.setHours(0, 0, 0, 0);
                                return sapDate >= weekStart && sapDate < weekEnd;
                            } catch (e) {
                                console.warn('Invalid date format:', sap.tanggal_pelaporan || sap.detected_at);
                                return false;
                            }
                        });
                        
                        console.log('SAP data loaded:', filteredSapData.length, 'items for week', weekValue, 'out of', newSapData.length, 'total');
                        
                        // Simpan semua data per week untuk count di tab
                        sapDataAllWeek = [...filteredSapData];
                        
                        // Urutkan semua data berdasarkan tanggal terbaru
                        const sortedSapDataAll = [...filteredSapData].sort((a, b) => {
                            const dateA = new Date(a.tanggal_pelaporan || a.detected_at || 0);
                            const dateB = new Date(b.tanggal_pelaporan || b.detected_at || 0);
                            return dateB - dateA; // Terbaru di atas
                        });
                        
                        // Filter data hari ini untuk sidebar
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        const todayStr = today.toISOString().split('T')[0];
                        
                        const sapDataToday = sortedSapDataAll.filter(sap => {
                            if (!sap.tanggal_pelaporan && !sap.detected_at) return false;
                            try {
                                const sapDate = new Date(sap.tanggal_pelaporan || sap.detected_at);
                                sapDate.setHours(0, 0, 0, 0);
                                const sapDateStr = sapDate.toISOString().split('T')[0];
                                return sapDateStr === todayStr;
                            } catch (e) {
                                return false;
                            }
                        });
                        
                        // Map: Hanya tampilkan 1000 data terbaru dari semua data per week
                        const sapDataForMap = sortedSapDataAll.slice(0, 1000);
                        
                        // Update global sapData (untuk map)
                        sapData = sapDataForMap;
                        
                        // Update filtered sidebar data (hanya data hari ini untuk sidebar)
                        filteredSidebarData.sap = sapDataToday;
                        sapDataForSidebar = sapDataToday;
                        
                        // Update tab counts (menggunakan semua data per week untuk count)
                        updateTabCounts();
                        
                        // Update sidebar list jika tab SAP aktif (tampilkan hanya data hari ini)
                        if (currentSidebarTab === 'sap') {
                            renderSapList(filteredSidebarData.sap);
                        }
                        
                        // Update map markers (hanya 1000 terbaru dari semua data per week)
                        updateSapMarkersOnMap(sapDataForMap);
                        
                        // Update evaluation alerts jika toggle evaluasi aktif
                        if (evaluationEnabled) {
                            showEvaluationAlerts();
                        }
                        
                        console.log(`Week total: ${sapDataAllWeek.length} SAP items | Sidebar (today): ${sapDataToday.length} SAP items | Map: ${sapDataForMap.length} SAP markers (limited to 1000)`);
                        
                        // Close SweetAlert loading
                        Swal.close();
                        
                        resolve(filteredSapData);
                    } else {
                        console.warn('No SAP data returned for week', weekValue, data);
                        // Set empty jika tidak ada data
                        sapDataAllWeek = [];
                        sapData = [];
                        sapDataForSidebar = [];
                        filteredSidebarData.sap = [];
                        updateTabCounts();
                        if (currentSidebarTab === 'sap') {
                            renderSapList([]);
                        }
                        updateSapMarkersOnMap([]);
                        
                        // Update evaluation alerts jika toggle evaluasi aktif
                        if (evaluationEnabled) {
                            showEvaluationAlerts();
                        }
                        
                        // Close SweetAlert loading
                        Swal.close();
                        
                        resolve([]);
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    
                    // Close loading SweetAlert
                    Swal.close();
                    
                    // Show error message
                    let errorMessage = 'Terjadi kesalahan saat memuat data SAP.';
                    if (error.name === 'AbortError') {
                        errorMessage = 'Request timeout. Data terlalu besar atau koneksi lambat. Silakan coba lagi.';
                        console.error('SAP data request timeout after 60 seconds');
                    } else {
                        console.error('Error loading SAP data:', error);
                        if (error.message) {
                            errorMessage = error.message;
                        }
                    }
                    
                    // Set empty pada error
                    sapDataAllWeek = [];
                    sapData = [];
                    sapDataForSidebar = [];
                    filteredSidebarData.sap = [];
                    updateTabCounts();
                    if (currentSidebarTab === 'sap') {
                        renderSapList([]);
                    }
                    
                    // Show error message to user
                    const sapTabCount = document.getElementById('sapTabCount');
                    if (sapTabCount) {
                        sapTabCount.textContent = '0';
                    }
                    
                    // Show error SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: errorMessage,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    
                    reject(error);
                });
        });
    }
    
    // Update SAP markers di map
    function updateSapMarkersOnMap(sapDataArray) {
        if (!hazardLayer) return;
        
        // Clear existing SAP markers (hanya yang dari SAP, bukan yang lain)
        const source = hazardLayer.getSource();
        const featuresToRemove = [];
        source.forEachFeature(function(feature) {
            // Hapus hanya feature SAP (yang punya task_number atau jenis_laporan)
            if (feature.get('task_number') || feature.get('jenis_laporan')) {
                featuresToRemove.push(feature);
            }
        });
        featuresToRemove.forEach(feature => source.removeFeature(feature));
        
        // OPTIMIZED: Batch rendering untuk SAP markers
        // Data yang diterima sudah dibatasi 1000 terbaru dari loadSapDataByWeek
        const MAX_SAP_MARKERS = 1000;
        const BATCH_SIZE = 100;
        
        if (sapDataArray && sapDataArray.length > 0) {
            // Pastikan data diurutkan berdasarkan tanggal terbaru (jika belum)
            const sortedData = [...sapDataArray].sort((a, b) => {
                const dateA = new Date(a.tanggal_pelaporan || a.detected_at || 0);
                const dateB = new Date(b.tanggal_pelaporan || b.detected_at || 0);
                return dateB - dateA; // Terbaru di atas
            });
            
            // Ambil maksimal 1000 data terbaru untuk map
            const limitedData = sortedData.slice(0, MAX_SAP_MARKERS);
            
            const source = hazardLayer.getSource();
            const features = [];
            let markerCount = 0;
            
            limitedData.forEach(function(sap) {
                if (markerCount >= MAX_SAP_MARKERS) {
                    return;
                }
                
                if (sap.location && sap.location.lat && sap.location.lng) {
                    const feature = new ol.Feature({
                        geometry: new ol.geom.Point(
                            ol.proj.fromLonLat([sap.location.lng, sap.location.lat])
                        ),
                        id: sap.id,
                        task_number: sap.task_number,
                        jenis_laporan: sap.jenis_laporan,
                        aktivitas_pekerjaan: sap.aktivitas_pekerjaan,
                        lokasi: sap.lokasi,
                        description: sap.description,
                        data: sap
                    });
                    features.push(feature);
                    markerCount++;
                }
            });
            
            // Batch add features
            if (features.length > 0) {
                let index = 0;
                function addBatch() {
                    const batch = features.slice(index, index + BATCH_SIZE);
                    if (batch.length > 0) {
                        source.addFeatures(batch);
                        index += BATCH_SIZE;
                        if (index < features.length) {
                            requestAnimationFrame(addBatch);
                        } else {
                            console.log('SAP markers updated on map:', features.length, 'markers displayed (out of', sapDataArray.length, 'total)');
                        }
                    }
                }
                requestAnimationFrame(addBatch);
            }
        } else {
            console.log('SAP markers updated on map: 0 markers');
        }
    }

    // ============================================
    // Employee Location Monitoring & Telegram Notification - REMOVED (no longer used)
    // All functions related to employee location monitoring have been removed
    
    // ============================================
    // Notification Panel - Risk Matrix Summary
    // ============================================
    
    // Function to get all area kerja features from all layers
    function getAllAreaKerjaFeatures() {
        const allFeatures = [];
        
        // Get features from window.areaKerjaLayers array
        if (window.areaKerjaLayers && Array.isArray(window.areaKerjaLayers)) {
            window.areaKerjaLayers.forEach(layer => {
                if (layer && layer.getSource) {
                    const features = layer.getSource().getFeatures();
                    allFeatures.push(...features);
                }
            });
        }
        
        // Also check areaKerjaBmo2PamaLayer if it exists
        if (typeof areaKerjaBmo2PamaLayer !== 'undefined' && areaKerjaBmo2PamaLayer) {
            const features = areaKerjaBmo2PamaLayer.getSource().getFeatures();
            allFeatures.push(...features);
        }
        
        return allFeatures;
    }
    
    // Function to count features by risk level
    function countFeaturesByRiskLevel() {
        const allFeatures = getAllAreaKerjaFeatures();
        
        const counts = {
            HIGH: [],
            MEDIUM: [],
            NORMAL: []
        };
        
        allFeatures.forEach(feature => {
            const riskLevel = feature.get('riskLevel');
            const lokasi = feature.get('lokasi') || feature.get('id_lokasi') || 'Unknown Location';
            const geometry = feature.getGeometry();
            
            if (riskLevel) {
                counts[riskLevel].push({
                    feature: feature,
                    lokasi: lokasi,
                    geometry: geometry
                });
            }
        });
        
        return counts;
    }
    
    // Function to update notification badge
    function updateNotificationBadge() {
        const badge = document.querySelector('.notification-badge');
        if (!badge) return;
        
        const counts = countFeaturesByRiskLevel();
        const totalHighRisk = counts.HIGH.length + counts.MEDIUM.length;
        
        if (totalHighRisk > 0) {
            badge.style.display = 'block';
            // Optionally show count in badge (if you want to display number)
            // badge.textContent = totalHighRisk > 99 ? '99+' : totalHighRisk;
        } else {
            badge.style.display = 'none';
        }
    }
    
    // Function to determine active matrix
    function getActiveMatrix() {
        // Check if daily operation plans layer (matrik area kerja) is visible
        if (dailyOperationPlansLayer && dailyOperationPlansLayer.getVisible()) {
            return 'area_kerja';
        }
        
        // Check if satellite layer (matrik CCTV) is active
        const satelliteCheckbox = document.getElementById('layerSatellite');
        if (satelliteCheckbox && satelliteCheckbox.checked) {
            return 'cctv';
        }
        
        // Check if terrain layer (Unit dan Orang) is active
        const terrainCheckbox = document.getElementById('layerTerrain');
        if (terrainCheckbox && terrainCheckbox.checked) {
            return 'unit_dan_orang';
        }
        
        // Default to risk matrix (area kerja risk)
        return 'risk';
    }
    
    // Function to render notification panel content
    function renderNotificationPanel() {
        const panelBody = document.getElementById('gmNotificationPanelBody');
        const panelTitle = document.querySelector('.gm-notification-panel-title');
        if (!panelBody) return;
        
        const activeMatrix = getActiveMatrix();
        
        // Update panel title based on active matrix
        if (panelTitle) {
            switch(activeMatrix) {
                case 'area_kerja':
                    panelTitle.textContent = 'Ringkasan Matriks Area Kerja';
                    break;
                case 'cctv':
                    panelTitle.textContent = 'Ringkasan Matriks CCTV';
                    break;
                case 'unit_dan_orang':
                    panelTitle.textContent = 'Ringkasan Matriks Unit dan Orang';
                    break;
                default:
                    panelTitle.textContent = 'Ringkasan Matrix Risk';
            }
        }
        
        // Render different content based on active matrix
        if (activeMatrix === 'area_kerja') {
            renderAreaKerjaNotification(panelBody);
            return;
        }
        
        if (activeMatrix === 'unit_dan_orang') {
            renderUnitDanOrangNotification(panelBody);
            return;
        }
        
        // Default: render risk matrix (existing code)
        const counts = countFeaturesByRiskLevel();
        
        let html = '';
        
        // Red (HIGH) category
        const redCount = counts.HIGH.length;
        html += `
            <div class="gm-notification-category ${redCount > 0 ? 'expanded' : ''}" data-risk="HIGH">
                <div class="gm-notification-category-header">
                    <div class="gm-notification-category-title">
                        <span class="gm-notification-color-indicator red"></span>
                        <span>Risk Tinggi (Merah)</span>
                        <i class="material-icons-outlined gm-notification-category-arrow" style="font-size: 18px; margin-left: 8px;">chevron_right</i>
                    </div>
                    <span class="gm-notification-category-count">${redCount}</span>
                </div>
                <div class="gm-notification-location-list">
                    ${redCount > 0 ? counts.HIGH.map((item, index) => `
                        <div class="gm-notification-location-item" data-lokasi="${item.lokasi}" data-index="${index}">
                            <i class="material-icons-outlined">location_on</i>
                            <span>${item.lokasi}</span>
                        </div>
                    `).join('') : '<div class="gm-notification-empty">Tidak ada lokasi</div>'}
                </div>
            </div>
        `;
        
        // Orange (MEDIUM) category
        const orangeCount = counts.MEDIUM.length;
        html += `
            <div class="gm-notification-category ${orangeCount > 0 ? 'expanded' : ''}" data-risk="MEDIUM">
                <div class="gm-notification-category-header">
                    <div class="gm-notification-category-title">
                        <span class="gm-notification-color-indicator orange"></span>
                        <span>Risk Sedang (Orange)</span>
                        <i class="material-icons-outlined gm-notification-category-arrow" style="font-size: 18px; margin-left: 8px;">chevron_right</i>
                    </div>
                    <span class="gm-notification-category-count">${orangeCount}</span>
                </div>
                <div class="gm-notification-location-list">
                    ${orangeCount > 0 ? counts.MEDIUM.map((item, index) => `
                        <div class="gm-notification-location-item" data-lokasi="${item.lokasi}" data-index="${index}">
                            <i class="material-icons-outlined">location_on</i>
                            <span>${item.lokasi}</span>
                        </div>
                    `).join('') : '<div class="gm-notification-empty">Tidak ada lokasi</div>'}
                </div>
            </div>
        `;
        
        // Green (NORMAL) category
        const greenCount = counts.NORMAL.length;
        html += `
            <div class="gm-notification-category ${greenCount > 0 ? 'expanded' : ''}" data-risk="NORMAL">
                <div class="gm-notification-category-header">
                    <div class="gm-notification-category-title">
                        <span class="gm-notification-color-indicator green"></span>
                        <span>Risk Normal (Hijau)</span>
                        <i class="material-icons-outlined gm-notification-category-arrow" style="font-size: 18px; margin-left: 8px;">chevron_right</i>
                    </div>
                    <span class="gm-notification-category-count">${greenCount}</span>
                </div>
                <div class="gm-notification-location-list">
                    ${greenCount > 0 ? counts.NORMAL.map((item, index) => `
                        <div class="gm-notification-location-item" data-lokasi="${item.lokasi}" data-index="${index}">
                            <i class="material-icons-outlined">location_on</i>
                            <span>${item.lokasi}</span>
                        </div>
                    `).join('') : '<div class="gm-notification-empty">Tidak ada lokasi</div>'}
                </div>
            </div>
        `;
        
        if (redCount === 0 && orangeCount === 0 && greenCount === 0) {
            html = '<div class="gm-notification-empty">Belum ada data area kerja yang dimuat</div>';
        }
        
        panelBody.innerHTML = html;
        
        // Add click handlers for category headers (expand/collapse)
        const categories = panelBody.querySelectorAll('.gm-notification-category');
        categories.forEach(category => {
            const header = category.querySelector('.gm-notification-category-header');
            if (header) {
                header.addEventListener('click', function(e) {
                    // Don't toggle if clicking on count badge
                    if (e.target.classList.contains('gm-notification-category-count')) {
                        return;
                    }
                    category.classList.toggle('expanded');
                });
            }
        });
        
        // Add click handlers for location items (navigate to location)
        const locationItems = panelBody.querySelectorAll('.gm-notification-location-item');
        locationItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const lokasi = this.getAttribute('data-lokasi');
                const index = parseInt(this.getAttribute('data-index'));
                const risk = this.closest('.gm-notification-category').getAttribute('data-risk');
                const counts = countFeaturesByRiskLevel();
                
                // Find the feature for this location using index
                const categoryData = counts[risk];
                if (categoryData && categoryData[index]) {
                    const locationData = categoryData[index];
                    if (locationData.geometry) {
                        navigateToLocation(locationData.geometry, locationData.feature);
                    }
                }
            });
        });
        
        // Update notification badge
        updateNotificationBadge();
    }
    
    // Function to render unit dan orang notification (jumlah unit dan orang berdasarkan tipe)
    function renderUnitDanOrangNotification(panelBody) {
        // Check if layers are visible
        const unitVisible = unitVehicleLayer && unitVehicleLayer.getVisible();
        const gpsVisible = userGpsLayer && userGpsLayer.getVisible();
        
        if (!unitVisible && !gpsVisible) {
            panelBody.innerHTML = '<div class="gm-notification-empty">Layer Unit dan Orang belum diaktifkan. Aktifkan layer "Unit dan Orang" terlebih dahulu.</div>';
            return;
        }
        
        // Get unit data
        const unitDataByType = {};
        let totalUnits = 0;
        
        if (unitVisible && unitVehicleLayer) {
            const unitSource = unitVehicleLayer.getSource();
            const unitFeatures = unitSource.getFeatures();
            
            unitFeatures.forEach(feature => {
                const unitData = feature.get('unitData');
                if (unitData) {
                    const vehicleType = unitData.vehicle_type || 'Unknown';
                    if (!unitDataByType[vehicleType]) {
                        unitDataByType[vehicleType] = {
                            count: 0,
                            units: []
                        };
                    }
                    unitDataByType[vehicleType].count++;
                    unitDataByType[vehicleType].units.push(unitData);
                    totalUnits++;
                }
            });
        }
        
        // Get GPS Orang data
        const orangDataByType = {};
        let totalOrang = 0;
        
        if (gpsVisible && userGpsLayer) {
            const orangSource = userGpsLayer.getSource();
            const orangFeatures = orangSource.getFeatures();
            
            orangFeatures.forEach(feature => {
                const userData = feature.get('userData');
                if (userData) {
                    // GPS Orang juga punya vehicle_type
                    const vehicleType = userData.vehicle_type || 'Unknown';
                    if (!orangDataByType[vehicleType]) {
                        orangDataByType[vehicleType] = {
                            count: 0,
                            orang: []
                        };
                    }
                    orangDataByType[vehicleType].count++;
                    orangDataByType[vehicleType].orang.push(userData);
                    totalOrang++;
                }
            });
        }
        
        // Build HTML
        let html = '';
        
        // Summary section
        html += `
            <div class="gm-notification-category expanded" style="background: #f8f9fa; margin-bottom: 12px; border-radius: 8px;">
                <div class="gm-notification-category-header">
                    <div class="gm-notification-category-title">
                        <i class="material-icons-outlined" style="font-size: 20px; color: #1a73e8;">dashboard</i>
                        <span>Ringkasan</span>
                    </div>
                </div>
                <div style="padding: 12px 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-weight: 500;">Total Unit:</span>
                        <span style="font-weight: 600; color: #1a73e8;">${totalUnits}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 500;">Total Orang:</span>
                        <span style="font-weight: 600; color: #10b981;">${totalOrang}</span>
                    </div>
                </div>
            </div>
        `;
        
        // Unit by type
        if (Object.keys(unitDataByType).length > 0) {
            html += `
                <div class="gm-notification-category expanded" style="margin-bottom: 12px;">
                    <div class="gm-notification-category-header">
                        <div class="gm-notification-category-title">
                            <i class="material-icons-outlined" style="font-size: 20px; color: #1a73e8;">directions_bus</i>
                            <span>Unit Berdasarkan Tipe</span>
                        </div>
                        <span class="gm-notification-category-count">${totalUnits}</span>
                    </div>
                    <div class="gm-notification-location-list">
            `;
            
            // Sort by count (descending)
            const sortedUnitTypes = Object.entries(unitDataByType).sort((a, b) => b[1].count - a[1].count);
            
            sortedUnitTypes.forEach(([vehicleType, data]) => {
                html += `
                    <div class="gm-notification-location-item" style="padding: 12px 20px; border-bottom: 1px solid #eee;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="flex: 1;">
                                <div style="font-weight: 500; margin-bottom: 4px;">${vehicleType}</div>
                                <div style="font-size: 12px; color: #666;">
                                    ${data.units.length} unit
                                </div>
                            </div>
                            <span style="font-size: 18px; font-weight: 600; color: #1a73e8;">${data.count}</span>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        } else if (unitVisible) {
            html += `
                <div class="gm-notification-category">
                    <div class="gm-notification-category-header">
                        <div class="gm-notification-category-title">
                            <i class="material-icons-outlined" style="font-size: 20px; color: #1a73e8;">directions_bus</i>
                            <span>Unit Berdasarkan Tipe</span>
                        </div>
                    </div>
                    <div class="gm-notification-location-list">
                        <div class="gm-notification-empty">Tidak ada data unit</div>
                    </div>
                </div>
            `;
        }
        
        // Orang by type
        if (Object.keys(orangDataByType).length > 0) {
            html += `
                <div class="gm-notification-category expanded" style="margin-bottom: 12px;">
                    <div class="gm-notification-category-header">
                        <div class="gm-notification-category-title">
                            <i class="material-icons-outlined" style="font-size: 20px; color: #10b981;">people</i>
                            <span>Orang Berdasarkan Tipe</span>
                        </div>
                        <span class="gm-notification-category-count">${totalOrang}</span>
                    </div>
                    <div class="gm-notification-location-list">
            `;
            
            // Sort by count (descending)
            const sortedOrangTypes = Object.entries(orangDataByType).sort((a, b) => b[1].count - a[1].count);
            
            sortedOrangTypes.forEach(([vehicleType, data]) => {
                html += `
                    <div class="gm-notification-location-item" style="padding: 12px 20px; border-bottom: 1px solid #eee;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="flex: 1;">
                                <div style="font-weight: 500; margin-bottom: 4px;">${vehicleType}</div>
                                <div style="font-size: 12px; color: #666;">
                                    ${data.orang.length} orang
                                </div>
                            </div>
                            <span style="font-size: 18px; font-weight: 600; color: #10b981;">${data.count}</span>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        } else if (gpsVisible) {
            html += `
                <div class="gm-notification-category">
                    <div class="gm-notification-category-header">
                        <div class="gm-notification-category-title">
                            <i class="material-icons-outlined" style="font-size: 20px; color: #10b981;">people</i>
                            <span>Orang Berdasarkan Tipe</span>
                        </div>
                    </div>
                    <div class="gm-notification-location-list">
                        <div class="gm-notification-empty">Tidak ada data orang</div>
                    </div>
                </div>
            `;
        }
        
        if (Object.keys(unitDataByType).length === 0 && Object.keys(orangDataByType).length === 0) {
            html = '<div class="gm-notification-empty">Belum ada data unit atau orang yang dimuat</div>';
        }
        
        panelBody.innerHTML = html;
        
        // Add click handlers for category headers (expand/collapse)
        const categories = panelBody.querySelectorAll('.gm-notification-category');
        categories.forEach(category => {
            const header = category.querySelector('.gm-notification-category-header');
            if (header) {
                header.addEventListener('click', function(e) {
                    // Don't toggle if clicking on count badge
                    if (e.target.classList.contains('gm-notification-category-count')) {
                        return;
                    }
                    category.classList.toggle('expanded');
                });
            }
        });
    }
    
    // Function to render area kerja notification (list pekerjaan dan area)
    function renderAreaKerjaNotification(panelBody) {
        if (!dailyOperationPlansLayer || !dailyOperationPlansLayer.getVisible()) {
            panelBody.innerHTML = '<div class="gm-notification-empty">Matrik Area Kerja belum diaktifkan. Aktifkan layer "Matriks Area Kerja" terlebih dahulu.</div>';
            return;
        }
        
        const source = dailyOperationPlansLayer.getSource();
        const features = source.getFeatures();
        
        if (features.length === 0) {
            panelBody.innerHTML = '<div class="gm-notification-empty">Belum ada data pekerjaan yang dimuat</div>';
            return;
        }
        
        // Group pekerjaan by lokasi/area
        const pekerjaanByArea = {};
        
        features.forEach((feature, index) => {
            const props = feature.getProperties();
            const lokasi = props.lokasi || 'Unknown Location';
            const detailLokasi = props.detail_lokasi || '';
            const pekerjaan = props.pekerjaan || 'N/A';
            const unitId = props.unit_id || 'N/A';
            const tanggal = props.tanggal || '';
            
            // Create area key (lokasi + detail_lokasi if available)
            const areaKey = detailLokasi ? `${lokasi} - ${detailLokasi}` : lokasi;
            
            if (!pekerjaanByArea[areaKey]) {
                pekerjaanByArea[areaKey] = {
                    lokasi: lokasi,
                    detailLokasi: detailLokasi,
                    pekerjaanList: [],
                    geometry: feature.getGeometry()
                };
            }
            
            pekerjaanByArea[areaKey].pekerjaanList.push({
                pekerjaan: pekerjaan,
                unitId: unitId,
                tanggal: tanggal,
                feature: feature,
                index: index
            });
        });
        
        // Convert to array and sort by lokasi
        const areas = Object.values(pekerjaanByArea).sort((a, b) => {
            return a.lokasi.localeCompare(b.lokasi);
        });
        
        let html = '';
        
        if (areas.length === 0) {
            html = '<div class="gm-notification-empty">Tidak ada data pekerjaan</div>';
        } else {
            areas.forEach((area, areaIndex) => {
                const areaTitle = area.detailLokasi ? `${area.lokasi} - ${area.detailLokasi}` : area.lokasi;
                const pekerjaanCount = area.pekerjaanList.length;
                
                html += `
                    <div class="gm-notification-category ${pekerjaanCount > 0 ? 'expanded' : ''}" data-area-index="${areaIndex}">
                        <div class="gm-notification-category-header">
                            <div class="gm-notification-category-title">
                                <i class="material-icons-outlined" style="font-size: 18px; margin-right: 8px;">work</i>
                                <span>${areaTitle}</span>
                                <i class="material-icons-outlined gm-notification-category-arrow" style="font-size: 18px; margin-left: 8px;">chevron_right</i>
                            </div>
                            <span class="gm-notification-category-count">${pekerjaanCount}</span>
                        </div>
                        <div class="gm-notification-location-list">
                            ${pekerjaanCount > 0 ? area.pekerjaanList.map((item, pekerjaanIndex) => `
                                <div class="gm-notification-location-item" 
                                     data-area-index="${areaIndex}" 
                                     data-pekerjaan-index="${pekerjaanIndex}"
                                     style="padding: 10px; border-bottom: 1px solid #eee;">
                                    <div style="display: flex; align-items: start; gap: 8px;">
                                        <i class="material-icons-outlined" style="font-size: 16px; color: #666; margin-top: 2px;">assignment</i>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 500; margin-bottom: 4px;">${item.pekerjaan}</div>
                                            <div style="font-size: 12px; color: #666;">
                                                <span>Unit: ${item.unitId}</span>
                                                ${item.tanggal ? `<span style="margin-left: 12px;">Tanggal: ${item.tanggal}</span>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('') : '<div class="gm-notification-empty">Tidak ada pekerjaan</div>'}
                        </div>
                    </div>
                `;
            });
        }
        
        panelBody.innerHTML = html;
        
        // Add click handlers for category headers (expand/collapse)
        const categories = panelBody.querySelectorAll('.gm-notification-category');
        categories.forEach(category => {
            const header = category.querySelector('.gm-notification-category-header');
            if (header) {
                header.addEventListener('click', function(e) {
                    // Don't toggle if clicking on count badge
                    if (e.target.classList.contains('gm-notification-category-count')) {
                        return;
                    }
                    category.classList.toggle('expanded');
                });
            }
        });
        
        // Add click handlers for pekerjaan items (navigate to location)
        const pekerjaanItems = panelBody.querySelectorAll('.gm-notification-location-item');
        pekerjaanItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const areaIndex = parseInt(this.getAttribute('data-area-index'));
                const pekerjaanIndex = parseInt(this.getAttribute('data-pekerjaan-index'));
                
                if (areas[areaIndex] && areas[areaIndex].pekerjaanList[pekerjaanIndex]) {
                    const pekerjaanData = areas[areaIndex].pekerjaanList[pekerjaanIndex];
                    if (pekerjaanData.feature && pekerjaanData.feature.getGeometry()) {
                        navigateToLocation(pekerjaanData.feature.getGeometry(), pekerjaanData.feature);
                    }
                }
            });
        });
    }
    
    // Function to navigate to a location on the map
    function navigateToLocation(geometry, feature) {
        if (!map || !geometry) return;
        
        const view = map.getView();
        
        // Get the center/extent of the geometry
        let center;
        if (geometry.getType() === 'Point') {
            center = geometry.getCoordinates();
        } else {
            // For polygons, get the center of the extent
            const extent = geometry.getExtent();
            center = ol.extent.getCenter(extent);
        }
        
        // Animate to location
        view.animate({
            center: center,
            zoom: 16,
            duration: 600
        });
        
        // Highlight the feature temporarily
        if (feature) {
            const originalStyle = feature.getStyle();
            const highlightStyle = new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: '#1a73e8',
                    width: 4
                }),
                fill: new ol.style.Fill({
                    color: 'rgba(26, 115, 232, 0.2)'
                })
            });
            
            feature.setStyle(highlightStyle);
            
            // Reset style after 3 seconds
            setTimeout(() => {
                if (originalStyle) {
                    feature.setStyle(originalStyle);
                } else {
                    // Use default style function if no original style
                    const riskLevel = feature.get('riskLevel');
                    if (riskLevel) {
                        feature.setStyle(getRiskBasedAreaKerjaStyle(feature));
                    }
                }
            }, 3000);
        }
        
        // Close notification panel (hanya jika tidak di-pin)
        const panel = document.getElementById('gmNotificationPanel');
        if (panel) {
            // Cek apakah panel di-pin menggunakan method atau class
            const panelPin = document.getElementById('gmNotificationPanelPin');
            const isPinned = (panel.isPinned && panel.isPinned()) || (panelPin && panelPin.classList.contains('pinned'));
            
            if (!isPinned) {
                panel.classList.remove('active');
            }
        }
    }
    
    // Initialize notification panel
    function initNotificationPanel() {
        const notificationBtn = document.getElementById('gmNotificationBtn');
        const notificationPanel = document.getElementById('gmNotificationPanel');
        const notificationPanelClose = document.getElementById('gmNotificationPanelClose');
        const notificationPanelPin = document.getElementById('gmNotificationPanelPin');
        
        if (!notificationBtn || !notificationPanel) return;
        
        // State untuk track apakah panel di-pin
        let isPanelPinned = false;
        
        // Toggle panel on button click
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isActive = notificationPanel.classList.contains('active');
            
            if (isActive) {
                // Hanya tutup jika tidak di-pin
                if (!isPanelPinned) {
                    notificationPanel.classList.remove('active');
                }
            } else {
                // Render panel content before showing
                renderNotificationPanel();
                notificationPanel.classList.add('active');
            }
        });
        
        // Pin/Unpin panel
        if (notificationPanelPin) {
            notificationPanelPin.addEventListener('click', function(e) {
                e.stopPropagation();
                isPanelPinned = !isPanelPinned;
                
                if (isPanelPinned) {
                    notificationPanelPin.classList.add('pinned');
                    notificationPanelPin.title = 'Unpin Panel';
                } else {
                    notificationPanelPin.classList.remove('pinned');
                    notificationPanelPin.title = 'Pin Panel';
                }
            });
        }
        
        // Close panel on close button click
        if (notificationPanelClose) {
            notificationPanelClose.addEventListener('click', function(e) {
                e.stopPropagation();
                // Reset pin state saat tutup
                isPanelPinned = false;
                if (notificationPanelPin) {
                    notificationPanelPin.classList.remove('pinned');
                    notificationPanelPin.title = 'Pin Panel';
                }
                notificationPanel.classList.remove('active');
            });
        }
        
        // Close panel when clicking outside (hanya jika tidak di-pin)
        document.addEventListener('click', function(e) {
            if (notificationPanel && notificationBtn &&
                !notificationPanel.contains(e.target) &&
                !notificationBtn.contains(e.target)) {
                // Hanya tutup jika tidak di-pin
                if (!isPanelPinned) {
                    notificationPanel.classList.remove('active');
                }
            }
        });
        
        // Simpan state pin di panel element untuk akses global
        notificationPanel.isPinned = function() {
            return isPanelPinned;
        };
        
        notificationPanel.setPinned = function(pinned) {
            isPanelPinned = pinned;
            if (notificationPanelPin) {
                if (pinned) {
                    notificationPanelPin.classList.add('pinned');
                    notificationPanelPin.title = 'Unpin Panel';
                } else {
                    notificationPanelPin.classList.remove('pinned');
                    notificationPanelPin.title = 'Pin Panel';
                }
            }
        };
        
        // Re-render panel when risk levels are updated (after risk calculation)
        // This will be called after features get their risk levels calculated
        const originalCalculateRisk = window.calculateRiskForAreaKerja;
        if (typeof originalCalculateRisk === 'function') {
            // Wrap the function to trigger panel update
            window.calculateRiskForAreaKerja = function(...args) {
                const result = originalCalculateRisk.apply(this, args);
                // Update panel if it's open and update badge
                setTimeout(() => {
                    if (notificationPanel.classList.contains('active')) {
                        renderNotificationPanel();
                    } else {
                        updateNotificationBadge();
                    }
                }, 500);
                return result;
            };
        }
        
        // Initial badge update
        setTimeout(updateNotificationBadge, 2000);
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initNotificationPanel, 1000);
            // Update badge periodically
            setInterval(updateNotificationBadge, 5000);
        });
    } else {
        setTimeout(initNotificationPanel, 1000);
        // Update badge periodically
        setInterval(updateNotificationBadge, 5000);
    }
    
    // Also re-render when layers are loaded
    const originalLoadLayers = window.loadAllLayers;
    if (typeof originalLoadLayers === 'function') {
        window.loadAllLayers = function(...args) {
            const result = originalLoadLayers.apply(this, args);
            setTimeout(() => {
                const panel = document.getElementById('gmNotificationPanel');
                if (panel && panel.classList.contains('active')) {
                    renderNotificationPanel();
                }
            }, 2000);
            return result;
        };
    }
    
    // ============================================
    // Intervensi Area Kerja
    // ============================================
    
    // Store current area kerja data for intervensi
    let currentIntervensiAreaKerja = null;
    let currentIntervensiLokasi = null;
    
    // Function to initialize Select2 for PIC dropdown
    function initializePICSelect2() {
        const picSelect = document.getElementById('intervensiPICAreaKerja');
        if (!picSelect) {
            console.error('PIC select element not found');
            return;
        }
        
        let retryCount = 0;
        const maxRetries = 50; // Try for 5 seconds (50 * 100ms)
        
        // Wait for jQuery and Select2 to be available
        function checkAndInit() {
            const $ = window.jQuery || window.$;
            
            if ($ && typeof $.fn.select2 !== 'undefined') {
                // jQuery and Select2 are available
                console.log('jQuery and Select2 are available, initializing...');
                initSelect2($);
            } else {
                retryCount++;
                if (retryCount < maxRetries) {
                    // Wait a bit and try again
                    console.log(`Waiting for jQuery and Select2 to load... (attempt ${retryCount}/${maxRetries})`);
                    setTimeout(checkAndInit, 100);
                } else {
                    console.error('jQuery or Select2 failed to load after', maxRetries, 'attempts');
                    console.error('jQuery available:', typeof $ !== 'undefined');
                    console.error('Select2 available:', $ && typeof $.fn.select2 !== 'undefined');
                }
            }
        }
        
        function initSelect2($) {
            if (!$ || !$.fn.select2) {
                console.error('jQuery or Select2 is still not available in initSelect2');
                return;
            }
        
            // Destroy existing Select2 instance if any
            if ($(picSelect).hasClass('select2-hidden-accessible')) {
                $(picSelect).select2('destroy');
            }
            
            // Clear select options
            picSelect.innerHTML = '<option value="">Pilih PIC...</option>';
            picSelect.disabled = false;
            
            // Initialize Select2 with AJAX search
            $(picSelect).select2({
            theme: 'bootstrap-5',
            placeholder: 'Ketik untuk mencari PIC (Pengawas)...',
            allowClear: true,
            width: '100%',
            minimumInputLength: 0,
            ajax: {
                url: `{{ url('cctv-data-control-room/users') }}`,
                type: 'GET',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    console.log('Select2 AJAX response:', data);
                    if (data.success && data.data) {
                        const results = data.data.map(user => ({
                            id: user.id,
                            text: user.text || (user.username + ' - ' + user.nama),
                            username: user.username,
                            nama: user.nama
                        }));
                        return {
                            results: results,
                            pagination: { more: false }
                        };
                    }
                    if (data.error) {
                        console.error('Select2 AJAX error:', data.error);
                        return {
                            results: [],
                            pagination: { more: false }
                        };
                    }
                    return {
                        results: data.results || [],
                        pagination: {
                            more: data.pagination && data.pagination.more
                        }
                    };
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Select2 AJAX error:', textStatus, errorThrown);
                    console.error('Response:', jqXHR.responseText);
                },
                cache: false
            },
            dropdownParent: $(picSelect).closest('.modal-body').length ? $(picSelect).closest('.modal-body') : $(document.body),
            language: {
                noResults: function() {
                    return "Tidak ada hasil ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                },
                inputTooShort: function() {
                    return "Ketik untuk mencari";
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            templateResult: function(user) {
                if (user.loading) {
                    return "Mencari...";
                }
                if (!user.text && user.username && user.nama) {
                    return user.username + ' - ' + user.nama;
                }
                return user.text || user.id;
            },
            templateSelection: function(user) {
                if (user.text) {
                    return user.text;
                }
                if (user.username && user.nama) {
                    return user.username + ' - ' + user.nama;
                }
                return user.id || '';
            }
        });
        
            // Trigger initial load when dropdown opens
            $(picSelect).on('select2:open', function() {
                const $select2 = $(picSelect).data('select2');
                if ($select2 && !$select2._request) {
                    $select2.trigger('query', { term: '' });
                }
            });
        }
        
        // Start checking for jQuery and Select2
        checkAndInit();
    }
    
    // Function to load intervensi modal with area kerja data
    function loadIntervensiAreaKerjaModal(areaKerja, lokasi) {
        // Set area kerja and lokasi - lokasi is required
        const lokasiValue = lokasi || '';
        const areaKerjaValue = areaKerja || '';
        
        document.getElementById('intervensiAreaKerja').value = areaKerjaValue;
        document.getElementById('intervensiLokasi').value = lokasiValue;
        document.getElementById('intervensiLokasiDisplay').value = lokasiValue;
        
        // Store for later use
        currentIntervensiAreaKerja = areaKerjaValue;
        currentIntervensiLokasi = lokasiValue;
        
        // Reset form but keep lokasi
        document.getElementById('intervensiIssueAreaKerja').value = '';
        
        // Clear PIC dropdown (Select2 will be initialized when modal is shown)
        const picSelect = document.getElementById('intervensiPICAreaKerja');
        if (picSelect) {
            // Destroy existing Select2 instance if any
            if (typeof $ !== 'undefined' && $(picSelect).hasClass('select2-hidden-accessible')) {
                $(picSelect).select2('destroy');
            }
            // Clear select options
            picSelect.innerHTML = '<option value="">Pilih PIC...</option>';
            picSelect.disabled = false;
        }
    }
    
    // Handle modal close to destroy Select2
    const intervensiAreaKerjaModal = document.getElementById('intervensiAreaKerjaModal');
    if (intervensiAreaKerjaModal) {
        intervensiAreaKerjaModal.addEventListener('hidden.bs.modal', function() {
            const picSelect = document.getElementById('intervensiPICAreaKerja');
            if (picSelect && typeof $ !== 'undefined' && $(picSelect).hasClass('select2-hidden-accessible')) {
                $(picSelect).select2('destroy');
            }
        });
    }
    
    // Handle button intervensi click
    document.addEventListener('DOMContentLoaded', function() {
        const btnIntervensiAreaKerja = document.getElementById('btnIntervensiAreaKerja');
        if (btnIntervensiAreaKerja) {
            btnIntervensiAreaKerja.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Get current area kerja data from global variable
                if (window.currentAreaKerjaForIntervensi && window.currentAreaKerjaForIntervensi.lokasi) {
                    // Store data for later use
                    const areaKerjaData = {
                        areaKerja: window.currentAreaKerjaForIntervensi.areaKerja || '',
                        lokasi: window.currentAreaKerjaForIntervensi.lokasi
                    };
                    
                    // Close summary modal first
                    const summaryModalElement = document.getElementById('areaKerjaSummaryModal');
                    const summaryModal = bootstrap.Modal.getInstance(summaryModalElement);
                    
                    // Function to open intervensi modal
                    const openIntervensiModal = function() {
                        // Load data and show intervensi modal
                        loadIntervensiAreaKerjaModal(areaKerjaData.areaKerja, areaKerjaData.lokasi);
                        
                        // Show intervensi modal
                        const intervensiModalElement = document.getElementById('intervensiAreaKerjaModal');
                        const intervensiModal = new bootstrap.Modal(intervensiModalElement);
                        intervensiModal.show();
                    };
                    
                    if (summaryModal) {
                        // Listen for when summary modal is fully hidden
                        const handleModalHidden = function() {
                            // Remove event listener
                            summaryModalElement.removeEventListener('hidden.bs.modal', handleModalHidden);
                            // Open intervensi modal
                            openIntervensiModal();
                        };
                        
                        summaryModalElement.addEventListener('hidden.bs.modal', handleModalHidden);
                        
                        // Hide summary modal
                        summaryModal.hide();
                    } else {
                        // If modal instance doesn't exist, just open intervensi modal directly
                        openIntervensiModal();
                    }
                } else {
                    // Fallback: prompt user
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Data lokasi tidak ditemukan. Silakan tutup dan buka kembali modal summary area kerja.'
                    });
                }
            });
        }
        
        // Handle modal shown event to initialize Select2 after modal is fully shown
        const intervensiAreaKerjaModalElement = document.getElementById('intervensiAreaKerjaModal');
        if (intervensiAreaKerjaModalElement) {
            intervensiAreaKerjaModalElement.addEventListener('shown.bs.modal', function() {
                // Initialize Select2 for PIC when modal is fully shown
                // Add a small delay to ensure all scripts are loaded
                setTimeout(function() {
                    initializePICSelect2();
                }, 200);
            });
        }
        
        // Handle submit intervensi form
        const submitIntervensiAreaKerjaBtn = document.getElementById('submitIntervensiAreaKerjaBtn');
        if (submitIntervensiAreaKerjaBtn) {
            console.log('Submit button found, attaching event listener');
            submitIntervensiAreaKerjaBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Submit button clicked');
                const form = document.getElementById('intervensiAreaKerjaForm');
                if (!form) {
                    console.error('Form not found');
                    return;
                }
                
                // Get form values
                const lokasi = document.getElementById('intervensiLokasi').value;
                const areaKerja = document.getElementById('intervensiAreaKerja').value || null;
                const picId = document.getElementById('intervensiPICAreaKerja').value;
                const issue = document.getElementById('intervensiIssueAreaKerja').value;
                
                // Validate form
                if (!lokasi) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Lokasi harus diisi.'
                    });
                    return;
                }
                
                if (!picId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'PIC (Pengawas) harus dipilih.'
                    });
                    return;
                }
                
                if (!issue || issue.trim() === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan!',
                        text: 'Issue harus diisi.'
                    });
                    return;
                }
                
                const formData = {
                    lokasi: lokasi,
                    area_kerja: areaKerja,
                    pic_id: picId,
                    issue: issue
                };
                
                console.log('Submitting intervensi:', formData);
                
                // Disable button
                const submitBtn = this;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';
                
                // Send AJAX request to save intervensi
                fetch(`{{ url('full-maps/api/intervensi-area-kerja') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                })
                    .then(response => {
                        // Check if response is ok
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Server error');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            // Show success message with option to open WhatsApp
                            const whatsappUrl = data.data?.whatsapp_url;
                            
                            if (whatsappUrl) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message || 'Intervensi berhasil dikirim!',
                                    showConfirmButton: true,
                                    showCancelButton: true,
                                    confirmButtonText: 'Buka WhatsApp',
                                    cancelButtonText: 'Tutup',
                                    confirmButtonColor: '#25D366'
                                }).then((result) => {
                                    // Close modal
                                    const modal = bootstrap.Modal.getInstance(document.getElementById('intervensiAreaKerjaModal'));
                                    if (modal) {
                                        modal.hide();
                                    }
                                    
                                    // Reset form
                                    form.reset();
                                    
                                    // Reset Select2 for PIC
                                    const picSelect = document.getElementById('intervensiPICAreaKerja');
                                    if (picSelect && typeof $ !== 'undefined' && $(picSelect).hasClass('select2-hidden-accessible')) {
                                        $(picSelect).val(null).trigger('change');
                                    }
                                    
                                    // Reset button
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = '<span class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">send</span>Kirim Intervensi';
                                    
                                    // Open WhatsApp if user clicked confirm
                                    if (result.isConfirmed && whatsappUrl) {
                                        window.open(whatsappUrl, '_blank');
                                    }
                                });
                            } else {
                                // No WhatsApp URL, just show success
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message || 'Intervensi berhasil dikirim!',
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    // Close modal
                                    const modal = bootstrap.Modal.getInstance(document.getElementById('intervensiAreaKerjaModal'));
                                    if (modal) {
                                        modal.hide();
                                    }
                                    
                                    // Reset form
                                    form.reset();
                                    
                                    // Reset Select2 for PIC
                                    const picSelect = document.getElementById('intervensiPICAreaKerja');
                                    if (picSelect && typeof $ !== 'undefined' && $(picSelect).hasClass('select2-hidden-accessible')) {
                                        $(picSelect).val(null).trigger('change');
                                    }
                                    
                                    // Reset button
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = '<span class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">send</span>Kirim Intervensi';
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan saat mengirim intervensi.',
                                footer: data.error ? '<small>' + data.error + '</small>' : ''
                            });
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<span class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">send</span>Kirim Intervensi';
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting intervensi:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.message || 'Terjadi kesalahan saat mengirim intervensi. Silakan coba lagi.'
                        });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">send</span>Kirim Intervensi';
                    });
            });
        }
    });
    
    // Charts menggunakan script dari template index.js
    // Script chart akan di-load dari build/js/index.js
</script>
<script src="{{ URL::asset('build/plugins/apexchart/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('build/js/index.js') }}"></script>
<script src="{{ URL::asset('build/plugins/peity/jquery.peity.min.js') }}"></script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- TourGuide JS Script -->
<script src="https://unpkg.com/@sjmc11/tourguidejs/dist/tour.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endsection



