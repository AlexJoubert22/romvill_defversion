import React from 'react';
import {
  AbsoluteFill,
  Img,
  interpolate,
  staticFile,
  useCurrentFrame,
} from 'remotion';
import { Audio } from '@remotion/media';
import { PrecisionGrid } from '../components/PrecisionGrid';
import { DimensionCard } from '../components/DimensionCard';

export const Scene3Dimensions: React.FC = () => {
  const frame = useCurrentFrame();

  // Active card progression based on voiceover timing
  // Voiceover mentions security, demographics, healthcare, mobility, zoning
  const activeIndex = Math.floor(
    interpolate(frame, [40, 100, 180, 260, 340], [0, 1, 2, 3, 4], {
      extrapolateLeft: 'clamp',
      extrapolateRight: 'clamp',
    })
  );

  const bgScale = interpolate(frame, [0, 460], [1.08, 1.0], {
    extrapolateRight: 'clamp',
  });

  return (
    <AbsoluteFill style={{ backgroundColor: '#0A0F18' }}>
      {/* Voiceover Track */}
      <Audio src={staticFile('audio/scene3.mp3')} volume={1} />

      {/* Background with aerial reconnaissance view */}
      <div
        style={{
          position: 'absolute',
          inset: 0,
          overflow: 'hidden',
        }}
      >
        <Img
          src={staticFile('images/alicante.jpg')}
          style={{
            width: '100%',
            height: '100%',
            objectFit: 'cover',
            transform: `scale(${bgScale})`,
            filter: 'brightness(0.2) contrast(1.25) saturate(0.7)',
          }}
        />
        <div
          style={{
            position: 'absolute',
            inset: 0,
            background:
              'radial-gradient(circle at center, rgba(10, 15, 24, 0.7) 0%, rgba(10, 15, 24, 0.98) 100%)',
          }}
        />
      </div>

      <PrecisionGrid
        locationLabel="5-DIMENSIONAL AUDIT SUITE"
        coordinates="CROSS-EXAMINING 5 SOURCES / DIMENSION"
        statusLabel="DEEP RECON"
      />

      <div
        style={{
          position: 'absolute',
          inset: 0,
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          justifyContent: 'center',
          zIndex: 10,
          padding: '0 50px',
        }}
      >
        {/* Section Header */}
        <div style={{ textAlign: 'center', marginBottom: 32 }}>
          <div
            style={{
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              gap: 12,
              marginBottom: 10,
            }}
          >
            <div style={{ width: 30, height: 1, backgroundColor: '#BFA15F' }} />
            <span
              style={{
                fontSize: 13,
                letterSpacing: '0.22em',
                fontWeight: 700,
                color: '#BFA15F',
                textTransform: 'uppercase',
              }}
            >
              METHODOLOGICAL RIGOR + FIELD RECONNAISSANCE
            </span>
            <div style={{ width: 30, height: 1, backgroundColor: '#BFA15F' }} />
          </div>

          <h2
            style={{
              fontSize: 44,
              fontWeight: 800,
              color: '#F2F5FA',
              letterSpacing: '-0.025em',
              margin: '0 0 8px 0',
            }}
          >
            The 5 Core Dimensions That Explain Everything
          </h2>
          <p
            style={{
              fontSize: 18,
              color: '#A9B4C6',
              margin: 0,
            }}
          >
            Documentary intelligence cross-checked with physical boots-on-the-ground inspection.
          </p>
        </div>

        {/* 5 Dimensions Grid */}
        <div
          style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(5, 1fr)',
            gap: 18,
            width: '100%',
            maxWidth: 1380,
          }}
        >
          <DimensionCard
            number="01"
            title="Zone Security"
            tagline="Official police records, civil guard incident trends & on-site physical patrol audits."
            badge="POLICE & FIELD"
            sources="5 Official Sources"
            isActive={activeIndex === 0}
            delay={10}
          />
          <DimensionCard
            number="02"
            title="Demographics"
            tagline="Real community makeup, wealth tiers, average disposable income, and social dynamics."
            badge="INE & TAX DATA"
            sources="Cadastre & Census"
            isActive={activeIndex === 1}
            delay={20}
          />
          <DimensionCard
            number="03"
            title="Healthcare"
            tagline="Emergency response radii, hospital coverage, and public vs. private medical facilities."
            badge="HEALTH NETWORK"
            sources="Regional Health Index"
            isActive={activeIndex === 2}
            delay={30}
          />
          <DimensionCard
            number="04"
            title="Connectivity"
            tagline="Rush-hour arterial bottlenecks, road networks, airport links, and true commute times."
            badge="TRANSIT TELEMETRY"
            sources="Mobility Registers"
            isActive={activeIndex === 3}
            delay={40}
          />
          <DimensionCard
            number="05"
            title="10-Yr Masterplan"
            tagline="Official municipal gazettes and upcoming infrastructure works. Zero speculation."
            badge="OFFICIAL GAZETTE"
            sources="Municipal Planning"
            isActive={activeIndex === 4}
            delay={50}
          />
        </div>
      </div>
    </AbsoluteFill>
  );
};
